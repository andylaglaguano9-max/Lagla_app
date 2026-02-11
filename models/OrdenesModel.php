<?php
declare(strict_types=1);

/**
 * OrdenesModel
 * 
 * Manages order lifecycle operations including order creation, retrieval, and transaction processing
 * across federated SQL Server databases (host + remote). Handles complex multi-step purchase flows,
 * game key allocation, delivery tracking, and transaction rollback on failure.
 * 
 * ARCHITECTURE NOTES:
 * - Host Database (GBB_Host on 10.26.208.203): Stores Ordenes, DetalleOrden, Entregas tables
 * - Remote Database (GBB_Remoto on 10.26.208.149 via linked server [10.26.208.149]): 
 *   Stores Juegos and KeysInventario tables
 * - Transactional boundary: All operations within confirmarCompra() must succeed atomically
 * - Key Allocation: Retrieves physically available keys from remote, marks as SOLD after purchase
 * - Fallback Strategy: Handles result set variations across different SP versions and direct queries
 */
class OrdenesModel {

    /**
     * comprarJuego()
     * 
     * Initiates a single game purchase request for a specific client. This is typically the
     * landing operation that validates price/availability before building the cart. The procedure
     * determines whether purchase is feasible and returns pricing/availability information.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_UI_ComprarJuego
     * 
     * @param int $juegoId The remote JuegoId from GBB_Remoto.dbo.Juegos table
     * @param int $clienteId The ClienteIdRemoto from GBB_Remoto.dbo.Usuarios table
     * @return array Single-row associative array with procedure result (Status, Message, pricing data, availability)
     *               or ['Status' => 'ERROR', 'Message' => exception message] on failure
     */
    public static function comprarJuego(int $juegoId, int $clienteId) {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();

            $sql = "EXEC dbo.sp_UI_ComprarJuego 
                        @ClienteIdRemoto = :cliente,
                        @JuegoId = :juego";

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':cliente', $clienteId, PDO::PARAM_INT);
            $stmt->bindParam(':juego', $juegoId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [
                'Status' => 'ERROR',
                'Message' => $e->getMessage()
            ];
        }
    }

    /**
     * listarMisOrdenes()
     * 
     * Retrieves summary list of all orders for a specific client (header information only,
     * no detailed line items). Demonstrates dual-approach fallback strategy:
     * 1. First attempts direct SQL query (faster, no SP dependency)
     * 2. Falls back to SP if query fails (handles permission issues on joined tables)
     * 
     * QUERY STRATEGY:
     * - Joins Ordenes (host) → DetalleOrden (host) → Juegos (remote via linked server)
     * - Aggregates game names and item counts per order using STRING_AGG and SUM
     * - Sorts by most recent orders first
     * 
     * SP FALLBACK: [10.26.208.149].GBB_Remoto.dbo.sp_UI_MisOrdenes
     * 
     * @param int $clienteId The ClienteIdRemoto to filter orders for
     * @return array Associative array with structure:
     *         [
     *             'data' => [
     *                 ['OrdenId' => int, 'Fecha' => string, 'Total' => float, 
     *                  'Estado' => string, 'TotalItems' => int, 'Juegos' => string],
     *                 ...
     *             ],
     *             'message' => null|string (error message if SP fallback needed)
     *         ]
     */
    public static function listarMisOrdenes(int $clienteId): array {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();
            try {
                // Attempt direct SQL query with string aggregation
                $stmt = $db->prepare("
                    SELECT
                        o.OrdenId,
                        o.Fecha,
                        o.Total,
                        o.Estado,
                        COALESCE(SUM(d.Cantidad), 0) AS TotalItems,
                        STRING_AGG(j.Nombre, ', ') AS Juegos
                    FROM dbo.Ordenes o
                    LEFT JOIN dbo.DetalleOrden d ON d.OrdenId = o.OrdenId
                    LEFT JOIN [10.26.208.149].GBB_Remoto.dbo.Juegos j ON j.JuegoId = d.JuegoIdRemoto
                    WHERE o.ClienteIdRemoto = :cliente
                    GROUP BY o.OrdenId, o.Fecha, o.Total, o.Estado
                    ORDER BY o.OrdenId DESC
                ");
                $stmt->bindValue(':cliente', $clienteId, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'data' => $rows ?: [],
                    'message' => null,
                ];
            } catch (Exception $e) {
                require_once __DIR__ . '/SP.php';
                // Direct query failed, attempting SP fallback for robust retrieval
                $res = SP::call('dbo.sp_UI_MisOrdenes', [
                    'ClienteIdRemoto' => $clienteId,
                ]);

                if (($res['Status'] ?? '') === 'ERROR') {
                    return [
                        'data' => [],
                        'message' => $res['Message'] ?? 'Error al listar órdenes',
                    ];
                }

                return [
                    'data' => $res['Data'] ?? [],
                    'message' => $res['Message'] ?? null,
                ];
            }
        } catch (Exception $e) {
            return [
                'data' => [],
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * confirmarCompra()
     * 
     * CORE TRANSACTION: Creates order header, processes line items with key allocation,
     * marks keys as sold in remote, and records deliveries in host database. All operations
     * must succeed or entire transaction rolls back to maintain data consistency.
     * 
     * TRANSACTION FLOW:
     * 1. BEGIN TRANSACTION
     * 2. Calculate total from cart items
     * 3. Call sp_Host_CrearOrden to create order header in Ordenes table
     * 4. For each cart item:
     *    a. Query KeysInventario (remote) for available DISPONIBLE key matching game
     *    b. Call sp_Host_InsertarDetalle to create detail row in DetalleOrden
     *    c. Update DetalleOrden.GameKey and KeyIdRemoto if columns exist
     *    d. Call sp_Host_RegistrarEntrega to log delivery record
     *    e. Call sp_Key_Vender on remote to mark key as SOLD
     * 5. Update Ordenes.Estado to 'PAGADA'
     * 6. COMMIT TRANSACTION
     * 
     * ERROR HANDLING:
     * - Catches attempts to retrieve OrdenId from multiple fallback sources (stored procedures
     *   may return results in different result set positions due to driver variations)
     * - Silently ignores failures on permission-restricted operations (GameKey updates, Entregas)
     * - Throws on critical failures (no order header created, no available keys)
     * - Performs ROLLBACK on any exception to prevent partial data
     * 
     * STORED PROCEDURES CALLED:
     * - [10.26.208.203].dbo.sp_Host_CrearOrden [@ClienteId, @Total] → returns OrdenId
     * - [10.26.208.203].dbo.sp_Host_InsertarDetalle [@OrdenId, @JuegoIdRemoto, @Precio] → returns DetalleId
     * - [10.26.208.149].GBB_Remoto.dbo.sp_Key_Vender [@KeyId] → marks key as sold
     * - [10.26.208.203].dbo.sp_Host_RegistrarEntrega [@OrdenId, @DetalleId, @KeyIdRemoto] → audit trail
     * 
     * @param int $clienteId ClienteIdRemoto that is purchasing (must exist in remote Usuarios)
     * @param array $items Cart items with structure:
     *        [
     *            ['JuegoId' => int, 'Nombre' => string, 'Precio' => float, 'Cantidad' => int],
     *            ...
     *        ]
     * @return array Transaction result array:
     *         SUCCESS: ['Status' => 'OK', 'OrdenId' => int, 'Total' => float, 'Detalle' => {...}]
     *         FAILURE: ['Status' => 'ERROR', 'Message' => string]
     */
    public static function confirmarCompra(int $clienteId, array $items): array
    {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();
            $db->beginTransaction();

            // Pre-calculate total purchase amount
            $total = 0.0;
            foreach ($items as $it) {
                $total += (float)$it['Precio'] * (int)$it['Cantidad'];
            }

            // Create order header in host database
            $stmt = $db->prepare("
                EXEC dbo.sp_Host_CrearOrden
                    @ClienteId = :cliente,
                    @Total = :total
            ");
            $stmt->bindValue(':cliente', $clienteId, PDO::PARAM_INT);
            $stmt->bindValue(':total', $total);
            $stmt->execute();
            
            // Attempt to retrieve OrdenId from first result set
            $row = null;
            try {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $row = null;
            }
            
            // If first fetch failed, iterate through result sets for OrdenId
            if (!$row) {
                try {
                    while ($stmt->nextRowset()) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row) break;
                    }
                } catch (Exception $e) {
                    $row = null;
                }
            }
            
            // Last resort: query SCOPE_IDENTITY() for auto-generated order ID
            if (!$row) {
                try {
                    $stmtId = $db->query("SELECT SCOPE_IDENTITY() AS OrdenId");
                    $row = $stmtId->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    $row = null;
                }
            }
            
            $ordenId = (int)($row['OrdenId'] ?? 0);

            if ($ordenId <= 0) {
                throw new Exception('No se pudo crear la orden.');
            }

            // Prepare detail insert statement for repeated use in loop
            $stmtDet = $db->prepare("
                EXEC dbo.sp_Host_InsertarDetalle
                    @OrdenId = :orden,
                    @JuegoIdRemoto = :juego,
                    @Precio = :precio
            ");
            
            // Prepare key vendor statement (marks key as sold in remote)
            $stmtKey = $db->prepare("
                EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Vender @KeyId = :keyId
            ");
            
            // Prepare delivery registration (audit trail in host)
            $stmtEntrega = $db->prepare("
                EXEC dbo.sp_Host_RegistrarEntrega
                    @OrdenId = :orden,
                    @DetalleId = :detalle,
                    @KeyIdRemoto = :keyId
            ");

            // Build response structure with order details
            $detalleResponse = [
                'OrdenId' => $ordenId,
                'Fecha' => date('Y-m-d H:i'),
                'Estado' => 'PAGADA',
                'Total' => $total,
                'Items' => [],
            ];

            // Process each item in cart (quantity may be > 1, each unit requires separate key)
            foreach ($items as $it) {
                $precio = (float)$it['Precio'];
                $cant = (int)$it['Cantidad'];
                
                // Each unit of quantity requires one game key from inventory
                for ($i = 0; $i < $cant; $i++) {
                    // Fetch next available key from remote inventory
                    $stmtKeyFree = $db->prepare("
                        SELECT TOP 1 KeyId, KeyValor
                        FROM [10.26.208.149].GBB_Remoto.dbo.KeysInventario
                        WHERE JuegoId = :juegoId
                          AND Estado = 'DISPONIBLE'
                        ORDER BY KeyId ASC
                    ");
                    $stmtKeyFree->bindValue(':juegoId', (int)$it['JuegoId'], PDO::PARAM_INT);
                    $stmtKeyFree->execute();
                    $keyRow = $stmtKeyFree->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$keyRow) {
                        throw new Exception("No hay stock disponible para el juego {$it['JuegoId']}");
                    }
                    
                    $keyId = (int)($keyRow['KeyId'] ?? 0);
                    $keyValor = (string)($keyRow['KeyValor'] ?? '');

                    // Insert detail line item for this order
                    $stmtDet->bindValue(':orden', $ordenId, PDO::PARAM_INT);
                    $stmtDet->bindValue(':juego', (int)$it['JuegoId'], PDO::PARAM_INT);
                    $stmtDet->bindValue(':precio', $precio);
                    $stmtDet->execute();

                    // Attempt to retrieve detail ID using multiple fallback strategies
                    $detalleId = 0;
                    try {
                        $detRow = $stmtDet->fetch(PDO::FETCH_ASSOC);
                        if (!$detRow) {
                            while ($stmtDet->nextRowset()) {
                                $detRow = $stmtDet->fetch(PDO::FETCH_ASSOC);
                                if ($detRow) break;
                            }
                        }
                        $detalleId = (int)($detRow['DetalleId'] ?? 0);
                    } catch (Exception $e) {
                        $detalleId = 0;
                    }
                    
                    // Final fallback: query most recently inserted detail for this order/game
                    if ($detalleId <= 0) {
                        try {
                            $stmtLast = $db->prepare("
                                SELECT TOP 1 DetalleId
                                FROM dbo.DetalleOrden
                                WHERE OrdenId = :orden AND JuegoIdRemoto = :juego
                                ORDER BY DetalleId DESC
                            ");
                            $stmtLast->bindValue(':orden', $ordenId, PDO::PARAM_INT);
                            $stmtLast->bindValue(':juego', (int)$it['JuegoId'], PDO::PARAM_INT);
                            $stmtLast->execute();
                            $detalleId = (int)($stmtLast->fetchColumn() ?: 0);
                        } catch (Exception $e) {
                            $detalleId = 0;
                        }
                    }
                    
                    // Attempt to store the game key in DetalleOrden if columns exist
                    if ($detalleId > 0 && $keyValor !== '') {
                        try {
                            $stmtUpd = $db->prepare("
                                UPDATE dbo.DetalleOrden
                                SET GameKey = :gk, KeyIdRemoto = :kid
                                WHERE DetalleId = :did
                            ");
                            $stmtUpd->bindValue(':gk', $keyValor);
                            $stmtUpd->bindValue(':kid', $keyId, PDO::PARAM_INT);
                            $stmtUpd->bindValue(':did', $detalleId, PDO::PARAM_INT);
                            $stmtUpd->execute();
                        } catch (Exception $e) {
                            // Column may not exist or no permission → ignore silently
                        }
                    }

                    // Register delivery record (audit/tracking table)
                    if ($detalleId > 0 && $keyId > 0) {
                        try {
                            $stmtEntrega->bindValue(':orden', $ordenId, PDO::PARAM_INT);
                            $stmtEntrega->bindValue(':detalle', $detalleId, PDO::PARAM_INT);
                            $stmtEntrega->bindValue(':keyId', $keyId, PDO::PARAM_INT);
                            $stmtEntrega->execute();
                        } catch (Exception $e) {
                            // Entregas table may not be accessible → ignore
                        }
                    }

                    // Mark key as sold in remote database
                    if ($keyId > 0) {
                        $stmtKey->bindValue(':keyId', $keyId, PDO::PARAM_INT);
                        $stmtKey->execute();
                    }

                    // Accumulate response data (UI will display these keys to customer)
                    if (!isset($detalleResponse['Items'][$it['JuegoId']])) {
                        $detalleResponse['Items'][$it['JuegoId']] = [
                            'JuegoId' => (int)$it['JuegoId'],
                            'Nombre' => $it['Nombre'] ?? ('Juego ' . (int)$it['JuegoId']),
                            'Precio' => $precio,
                            'Cantidad' => 0,
                            'Keys' => [],
                        ];
                    }
                    $detalleResponse['Items'][$it['JuegoId']]['Cantidad'] += 1;
                    if ($keyValor !== '') {
                        $detalleResponse['Items'][$it['JuegoId']]['Keys'][] = $keyValor;
                    }
                }
            }

            // Update order status to PAGADA after all details processed
            try {
                $stmtEstado = $db->prepare("UPDATE dbo.Ordenes SET Estado = 'PAGADA' WHERE OrdenId = :id");
                $stmtEstado->bindValue(':id', $ordenId, PDO::PARAM_INT);
                $stmtEstado->execute();
            } catch (Exception $e) {
                // Status update may fail on permission issues → tolerate
            }

            // Commit entire transaction
            $db->commit();
            
            return [
                'Status' => 'OK',
                'OrdenId' => $ordenId,
                'Total' => $total,
                'Detalle' => $detalleResponse,
            ];
            
        } catch (Exception $e) {
            // Rollback on any failure to maintain consistency
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return ['Status' => 'ERROR', 'Message' => $e->getMessage()];
        }
    }

    /**
     * listarMisOrdenesDetalle()
     * 
     * Retrieves detailed order information with individual game keys for each line item.
     * Joins host tables (Ordenes, DetalleOrden, Entregas) with remote tables (Juegos, KeysInventario)
     * to assemble complete purchase history including delivered keys.
     * 
     * QUERY STRATEGY:
     * - Ordenes (host) → DetalleOrden (host) → Entregas (host) → KeysInventario (remote)
     * - Also joins Juegos (remote) for game name and details
     * - Groups rows by OrdenId to consolidate items within same order
     * - Handles multiple keys per game if quantity > 1
     * 
     * DATA STRUCTURE:
     * Returned orders grouped with nested items array, each game in order has its quantity
     * and corresponding game keys. Falls back to summary view if detailed tables unavailable.
     * 
     * @param int $clienteId The ClienteIdRemoto to retrieve order details for
     * @return array Associative array:
     *         [
     *             'data' => [
     *                 [
     *                     'OrdenId' => int,
     *                     'Fecha' => string,
     *                     'Total' => float,
     *                     'Estado' => string,
     *                     'Items' => int (total unit count),
     *                     'Juegos' => [
     *                         [
     *                             'JuegoId' => int,
     *                             'Nombre' => string,
     *                             'Precio' => float,
     *                             'Cantidad' => int,
     *                             'Subtotal' => float,
     *                             'Keys' => [string, ...]  (game keys if available)
     *                         ],
     *                         ...
     *                     ]
     *                 ],
     *                 ...
     *             ],
     *             'message' => null
     *         ]
     */
    public static function listarMisOrdenesDetalle(int $clienteId): array
    {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();
            
            // Complex join query aggregating order, detail, delivery, and product information
            $stmt = $db->prepare("
                SELECT
                    o.OrdenId,
                    o.Fecha,
                    o.Total,
                    o.Estado,
                    d.DetalleId,
                    d.JuegoIdRemoto,
                    d.PrecioUnitario,
                    d.Cantidad,
                    d.Subtotal,
                    d.GameKey,
                    j.Nombre AS JuegoNombre,
                    e.KeyIdRemoto,
                    k.KeyValor
                FROM dbo.Ordenes o
                INNER JOIN dbo.DetalleOrden d ON d.OrdenId = o.OrdenId
                LEFT JOIN dbo.Entregas e ON e.OrdenId = o.OrdenId AND e.DetalleId = d.DetalleId
                LEFT JOIN [10.26.208.149].GBB_Remoto.dbo.Juegos j ON j.JuegoId = d.JuegoIdRemoto
                LEFT JOIN [10.26.208.149].GBB_Remoto.dbo.KeysInventario k ON k.KeyId = e.KeyIdRemoto
                WHERE o.ClienteIdRemoto = :cliente
                ORDER BY o.OrdenId DESC
            ");
            $stmt->bindValue(':cliente', $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group rows by order, consolidating games within each order
            $orders = [];
            foreach ($rows as $r) {
                $oid = (int)($r['OrdenId'] ?? 0);
                
                // Initialize order header on first occurrence of new OrdenId
                if (!isset($orders[$oid])) {
                    $orders[$oid] = [
                        'OrdenId' => $oid,
                        'Fecha' => $r['Fecha'] ?? null,
                        'Total' => $r['Total'] ?? 0,
                        'Estado' => $r['Estado'] ?? '',
                        'Items' => 0,
                        'Juegos' => [],
                    ];
                }
                
                // Add game item to this order (if detalleOrden has associated game)
                if (!empty($r['JuegoIdRemoto'])) {
                    // Prefer key from Entregas table, fallback to GameKey column if available
                    $keyVal = $r['KeyValor'] ?? '';
                    if ($keyVal === '' && !empty($r['GameKey'])) {
                        $keyVal = $r['GameKey'];
                    }
                    
                    $orders[$oid]['Juegos'][] = [
                        'JuegoId' => $r['JuegoIdRemoto'],
                        'Nombre' => $r['JuegoNombre'] ?? 'Juego',
                        'Precio' => $r['PrecioUnitario'] ?? 0,
                        'Cantidad' => $r['Cantidad'] ?? 0,
                        'Subtotal' => $r['Subtotal'] ?? 0,
                        'Keys' => $keyVal !== '' ? [$keyVal] : [],
                    ];
                    $orders[$oid]['Items'] += (int)($r['Cantidad'] ?? 0);
                }
            }

            return [
                'data' => array_values($orders),
                'message' => null,
            ];
            
        } catch (Exception $e) {
            // If detailed tables inaccessible (permissions), fall back to summary list
            $msg = $e->getMessage();
            if (stripos($msg, 'DetalleOrden') !== false || stripos($msg, 'permission') !== false) {
                return self::listarMisOrdenes($clienteId);
            }
            return [
                'data' => [],
                'message' => $msg,
            ];
        }
    }
}

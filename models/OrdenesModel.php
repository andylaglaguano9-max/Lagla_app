<?php
class OrdenesModel {

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

    public static function listarMisOrdenes(int $clienteId): array {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();
            try {
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

    public static function confirmarCompra(int $clienteId, array $items): array
    {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();
            $db->beginTransaction();

            $total = 0.0;
            foreach ($items as $it) {
                $total += (float)$it['Precio'] * (int)$it['Cantidad'];
            }

            $stmt = $db->prepare("
                EXEC dbo.sp_Host_CrearOrden
                    @ClienteId = :cliente,
                    @Total = :total
            ");
            $stmt->bindValue(':cliente', $clienteId, PDO::PARAM_INT);
            $stmt->bindValue(':total', $total);
            $stmt->execute();
            $row = null;
            try {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $row = null;
            }
            if (!$row) {
                // Intentar con siguientes resultsets si el driver no devuelve campos en el primero
                try {
                    while ($stmt->nextRowset()) {
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row) break;
                    }
                } catch (Exception $e) {
                    $row = null;
                }
            }
            if (!$row) {
                // Fallback: obtener el último identity de esta sesión
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

            $stmtDet = $db->prepare("
                EXEC dbo.sp_Host_InsertarDetalle
                    @OrdenId = :orden,
                    @JuegoIdRemoto = :juego,
                    @Precio = :precio
            ");
            $stmtKey = $db->prepare("
                EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Vender @KeyId = :keyId
            ");
            $stmtEntrega = $db->prepare("
                EXEC dbo.sp_Host_RegistrarEntrega
                    @OrdenId = :orden,
                    @DetalleId = :detalle,
                    @KeyIdRemoto = :keyId
            ");

            $detalleResponse = [
                'OrdenId' => $ordenId,
                'Fecha' => date('Y-m-d H:i'),
                'Estado' => 'PAGADA',
                'Total' => $total,
                'Items' => [],
            ];

            foreach ($items as $it) {
                $precio = (float)$it['Precio'];
                $cant = (int)$it['Cantidad'];
                for ($i = 0; $i < $cant; $i++) {
                    // Obtener key libre en remoto
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

                    $stmtDet->bindValue(':orden', $ordenId, PDO::PARAM_INT);
                    $stmtDet->bindValue(':juego', (int)$it['JuegoId'], PDO::PARAM_INT);
                    $stmtDet->bindValue(':precio', $precio);
                    $stmtDet->execute();

                    // Intentar obtener DetalleId y guardar key en host si existe columna
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
                    if ($detalleId <= 0) {
                        // Fallback: obtener el último detalle insertado para esta orden/juego
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
                            // Si no existe columna o no hay permiso, ignorar
                        }
                    }

                    if ($detalleId > 0 && $keyId > 0) {
                        try {
                            $stmtEntrega->bindValue(':orden', $ordenId, PDO::PARAM_INT);
                            $stmtEntrega->bindValue(':detalle', $detalleId, PDO::PARAM_INT);
                            $stmtEntrega->bindValue(':keyId', $keyId, PDO::PARAM_INT);
                            $stmtEntrega->execute();
                        } catch (Exception $e) {
                            // Si no hay permisos en entregas, ignorar
                        }
                    }

                    // Marcar key como vendida en remoto
                    if ($keyId > 0) {
                        $stmtKey->bindValue(':keyId', $keyId, PDO::PARAM_INT);
                        $stmtKey->execute();
                    }

                    // Guardar detalle en respuesta para mostrar al usuario
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

            try {
                $stmtEstado = $db->prepare("UPDATE dbo.Ordenes SET Estado = 'PAGADA' WHERE OrdenId = :id");
                $stmtEstado->bindValue(':id', $ordenId, PDO::PARAM_INT);
                $stmtEstado->execute();
            } catch (Exception $e) {
                // Si no hay permisos, no bloquear
            }

            $db->commit();
            return [
                'Status' => 'OK',
                'OrdenId' => $ordenId,
                'Total' => $total,
                'Detalle' => $detalleResponse,
            ];
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            return ['Status' => 'ERROR', 'Message' => $e->getMessage()];
        }
    }

    public static function listarMisOrdenesDetalle(int $clienteId): array
    {
        try {
            require_once __DIR__ . '/DB.php';
            $db = DB::conn();
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

            $orders = [];
            foreach ($rows as $r) {
                $oid = (int)($r['OrdenId'] ?? 0);
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
                if (!empty($r['JuegoIdRemoto'])) {
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
            // Fallback si no hay permisos en DetalleOrden
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

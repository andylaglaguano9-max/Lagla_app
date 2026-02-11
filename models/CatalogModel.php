<?php
declare(strict_types=1);

require_once __DIR__ . '/SP.php';

class CatalogModel
{
    public static function listarJuegos(): array
    {
        $res = SP::call('dbo.sp_Catalogo_ListarJuegos');

        // Si Data existe y tiene filas → todo OK
        if (isset($res['Data']) && is_array($res['Data']) && count($res['Data']) > 0) {
            $data = $res['Data'];
            // Enriquecer con stock real desde inventario (si existe)
            try {
                require_once __DIR__ . '/InventarioModel.php';
                $inventario = InventarioModel::listarInventario();
                $map = [];
                foreach ($inventario as $i) {
                    $jid = (int)($i['JuegoId'] ?? 0);
                    if ($jid > 0) {
                        $map[$jid] = $i;
                    }
                }
                if (!empty($map)) {
                    foreach ($data as &$row) {
                        $jid = (int)($row['JuegoId'] ?? $row['IdJuego'] ?? $row['id_juego'] ?? $row['id'] ?? 0);
                        if ($jid > 0 && isset($map[$jid])) {
                            $row['StockDisponible'] = $map[$jid]['Disponibles'] ?? null;
                            $row['Stock'] = $map[$jid]['Disponibles'] ?? ($row['Stock'] ?? null);
                            $row['TotalKeys'] = $map[$jid]['TotalKeys'] ?? null;
                            $row['Vendidas'] = $map[$jid]['Vendidas'] ?? null;
                        }
                    }
                    unset($row);
                }
            } catch (Exception $e) {
                // Si falla inventario, devolver catálogo base
            }
            return $data;
        }

        // Si no hay datos, guardar mensaje de error (si existe)
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['db_offline_message'] =
                $res['Message'] ?? 'Error al generar catálogo';
        }

        return [];
    }

    public static function obtenerDetalleJuego(int $juegoId)
    {
        require_once __DIR__ . '/DB.php';
        $db = DB::conn();

        $sql = "
            SELECT
                j.JuegoId,
                j.Nombre,
                j.Descripcion,
                j.ImagenUrl,
                j.Precio,
                p.Nombre AS Plataforma
            FROM [10.26.208.149].GBB_Remoto.dbo.Juegos j
            INNER JOIN [10.26.208.149].GBB_Remoto.dbo.Plataformas p
                ON j.PlataformaId = p.PlataformaId
            WHERE j.JuegoId = ?
              AND j.Estado = 1
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$juegoId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

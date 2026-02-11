<?php
declare(strict_types=1);

/**
 * CatalogModel
 * 
 * Modelo que gestiona el catálogo de juegos visible a los usuarios.
 * Obtiene datos del catálogo mediante procedimiento almacenado y enriquece
 * la información con datos de inventario de keys disponibles.
 */

require_once __DIR__ . '/SP.php';

class CatalogModel
{
    /**
     * listarJuegos()
     * 
     * Obtiene el listado completo de juegos del catálogo para mostrar a usuarios.
     * Invoca el procedimiento almacenado sp_Catalogo_ListarJuegos que retorna
     * información básica de juegos. Posteriormente enriquece los datos consultando
     * el inventario para agregar información de stock disponible y keys vendidas.
     * 
     * Si falla la consulta de inventario, devuelve los datos básicos del catálogo.
     * Si falla la consulta del catálogo, almacena el mensaje de error en sesión
     * y retorna un array vacío.
     * 
     * Procedimiento: dbo.sp_Catalogo_ListarJuegos
     * 
     * @return array Lista de juegos con datos enriquecidos de inventario
     */
    public static function listarJuegos(): array
    {
        // Invoca el procedimiento almacenado del catálogo
        $res = SP::call('dbo.sp_Catalogo_ListarJuegos');

        // Verifica que la respuesta contenga datos válidos
        if (isset($res['Data']) && is_array($res['Data']) && count($res['Data']) > 0) {
            $data = $res['Data'];
            
            // Intenta enriquecer con datos de inventario
            try {
                require_once __DIR__ . '/InventarioModel.php';
                // Obtiene el inventario de keys disponibles
                $inventario = InventarioModel::listarInventario();
                
                // Crea un mapa indexado por JuegoId para búsquedas rápidas
                $map = [];
                foreach ($inventario as $i) {
                    $jid = (int)($i['JuegoId'] ?? 0);
                    if ($jid > 0) {
                        $map[$jid] = $i;
                    }
                }
                
                // Enriquece los datos del catálogo con información de inventario
                if (!empty($map)) {
                    foreach ($data as &$row) {
                        // Intenta obtener el ID del juego con diferentes posibles nombres de columna
                        $jid = (int)($row['JuegoId'] ?? $row['IdJuego'] ?? $row['id_juego'] ?? $row['id'] ?? 0);
                        if ($jid > 0 && isset($map[$jid])) {
                            // Agrega datos de stock y vendidas
                            $row['StockDisponible'] = $map[$jid]['Disponibles'] ?? null;
                            $row['Stock'] = $map[$jid]['Disponibles'] ?? ($row['Stock'] ?? null);
                            $row['TotalKeys'] = $map[$jid]['TotalKeys'] ?? null;
                            $row['Vendidas'] = $map[$jid]['Vendidas'] ?? null;
                        }
                    }
                    unset($row);
                }
            } catch (Exception $e) {
                // Si falla inventario, devolver catálogo base sin información de stock
            }
            return $data;
        }

        // Si no hay datos, guardar mensaje de error en sesión para mostrar al usuario
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['db_offline_message'] =
                $res['Message'] ?? 'Error al generar catálogo';
        }

        return [];
    }

    /**
     * obtenerDetalleJuego()
     * 
     * Obtiene la información completa de un juego específico del catálogo.
     * Consulta directamente la tabla Juegos con JOIN a Plataformas para obtener
     * datos como nombre, descripción, precio, imagen y plataforma.
     * Solo retorna juegos en estado activo (Estado = 1).
     * 
     * Tablas: [10.26.208.149].GBB_Remoto.dbo.Juegos
     *         [10.26.208.149].GBB_Remoto.dbo.Plataformas
     * 
     * @param int $juegoId Identificador del juego a consultar
     * @return array|false Array con detalles del juego o false si no existe
     */
    public static function obtenerDetalleJuego(int $juegoId)
    {
        require_once __DIR__ . '/DB.php';
        $db = DB::conn();

        // Consulta datos del juego con JOIN a plataformas
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

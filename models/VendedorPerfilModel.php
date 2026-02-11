<?php
/**
 * VendedorPerfilModel
 * 
 * Modelo que gestiona la información y operaciones del perfil de vendedor.
 * Realiza operaciones de publicación, consulta de keys, ventas y actualizaciones
 * de inventario por parte de vendedores.
 */

require_once __DIR__ . '/DB.php';

class VendedorPerfilModel
{
    /**
     * resumen()
     * 
     * Obtiene un resumen estadístico del perfil del vendedor.
     * Calcula el total de keys publicadas, vendidas e ingresos totales.
     * 
     * Tabla: [10.26.208.149].GBB_Remoto.dbo.KeysInventario
     * 
     * @param int $vendedorId Identificador del vendedor
     * @return array Array con estadísticas: KeysPublicadas, KeysVendidas, Ingresos
     */
    public static function resumen(int $vendedorId): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            SELECT
                COUNT(*) AS KeysPublicadas,
                SUM(CASE WHEN Estado = 'VENDIDA' THEN 1 ELSE 0 END) AS KeysVendidas,
                SUM(CASE WHEN Estado = 'VENDIDA' THEN Precio ELSE 0 END) AS Ingresos
            FROM [10.26.208.149].GBB_Remoto.dbo.KeysInventario
            WHERE VendedorId = :id
        ");
        $stmt->bindValue(':id', $vendedorId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        return [
            'KeysPublicadas' => (int)($row['KeysPublicadas'] ?? 0),
            'KeysVendidas' => (int)($row['KeysVendidas'] ?? 0),
            'Ingresos' => (float)($row['Ingresos'] ?? 0),
        ];
    }

    /**
     * publicarKey()
     * 
     * Publica una nueva key de juego para su venta.
     * Invoca el procedimiento almacenado sp_Key_Publicar que crea la key
     * con estado PENDIENTE para revisión por administrador.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Key_Publicar
     * 
     * @param int $juegoId Identificador del juego
     * @param int $vendedorId Identificador del vendedor
     * @param string $keyValor Valor/código de la key de licencia
     * @param float $precio Precio de venta de la key
     * @return bool true si la publicación fue exitosa, false en caso de error
     */
    public static function publicarKey(int $juegoId, int $vendedorId, string $keyValor, float $precio): bool
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Publicar
                @JuegoId = :juegoId,
                @VendedorId = :vendedorId,
                @KeyValor = :keyValor,
                @Precio = :precio
        ");
        $stmt->bindValue(':juegoId', $juegoId, PDO::PARAM_INT);
        $stmt->bindValue(':vendedorId', $vendedorId, PDO::PARAM_INT);
        $stmt->bindValue(':keyValor', $keyValor);
        $stmt->bindValue(':precio', $precio);
        return $stmt->execute();
    }

    /**
     * misKeys()
     * 
     * Obtiene el listado de todas las keys publicadas por el vendedor.
     * Invoca el procedimiento almacenado sp_Vendedor_MisKeys.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Vendedor_MisKeys
     * 
     * @param int $vendedorId Identificador del vendedor
     * @return array Lista de keys publicadas con su estado, precio y detalles
     */
    public static function misKeys(int $vendedorId): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Vendedor_MisKeys @VendedorId = :id");
        $stmt->bindValue(':id', $vendedorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * misVentas()
     * 
     * Obtiene el historial de ventas del vendedor.
     * Invoca el procedimiento almacenado sp_Vendedor_MisVentas que retorna
     * todas las keys vendidas con información de comprador, fecha y monto.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Vendedor_MisVentas
     * 
     * @param int $vendedorId Identificador del vendedor
     * @return array Lista de ventas realizadas con detalles
     */
    public static function misVentas(int $vendedorId): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Vendedor_MisVentas @VendedorId = :id");
        $stmt->bindValue(':id', $vendedorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * actualizarPendiente()
     * 
     * Actualiza una key en estado PENDIENTE modificando su valor y precio.
     * Solo permite actualizaciones de keys que aún no han sido approved.
     * 
     * Tabla: [10.26.208.149].GBB_Remoto.dbo.KeysInventario
     * 
     * @param int $keyId Identificador de la key a actualizar
     * @param int $vendedorId Identificador del vendedor propietario
     * @param string $keyValor Nuevo valor/código de la key
     * @param float $precio Nuevo precio
     * @return bool true si la actualización fue exitosa, false en caso de error
     */
    public static function actualizarPendiente(int $keyId, int $vendedorId, string $keyValor, float $precio): bool
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            UPDATE [10.26.208.149].GBB_Remoto.dbo.KeysInventario
            SET KeyValor = :keyValor, Precio = :precio
            WHERE KeyId = :keyId
              AND VendedorId = :vendedorId
              AND Estado = 'PENDIENTE'
        ");
        $stmt->bindValue(':keyValor', $keyValor);
        $stmt->bindValue(':precio', $precio);
        $stmt->bindValue(':keyId', $keyId, PDO::PARAM_INT);
        $stmt->bindValue(':vendedorId', $vendedorId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * eliminarPendiente()
     * 
     * Elimina una key en estado PENDIENTE del inventario.
     * Solo permite eliminación de keys que aún no han sido aprobadas.
     * 
     * Tabla: [10.26.208.149].GBB_Remoto.dbo.KeysInventario
     * 
     * @param int $keyId Identificador de la key a eliminar
     * @param int $vendedorId Identificador del vendedor propietario
     * @return bool true si la eliminación fue exitosa, false en caso de error
     */
    public static function eliminarPendiente(int $keyId, int $vendedorId): bool
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            DELETE FROM [10.26.208.149].GBB_Remoto.dbo.KeysInventario
            WHERE KeyId = :keyId
              AND VendedorId = :vendedorId
              AND Estado = 'PENDIENTE'
        ");
        $stmt->bindValue(':keyId', $keyId, PDO::PARAM_INT);
        $stmt->bindValue(':vendedorId', $vendedorId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

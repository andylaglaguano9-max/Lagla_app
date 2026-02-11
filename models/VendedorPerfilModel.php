<?php
require_once __DIR__ . '/DB.php';

class VendedorPerfilModel
{
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

    public static function misKeys(int $vendedorId): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Vendedor_MisKeys @VendedorId = :id");
        $stmt->bindValue(':id', $vendedorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function misVentas(int $vendedorId): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Vendedor_MisVentas @VendedorId = :id");
        $stmt->bindValue(':id', $vendedorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

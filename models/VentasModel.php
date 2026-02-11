<?php
require_once __DIR__ . '/DB.php';

class VentasModel {

    public static function listarVentas() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Ventas_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function anularVenta(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Venta_Anular @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

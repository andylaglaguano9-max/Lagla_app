<?php
require_once __DIR__ . '/DB.php';

class InventarioModel {

    public static function listarInventario() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Inventario_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function desactivarKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Desactivar @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function listarPendientes() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Keys_Pendientes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function aprobarKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Aprobar @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function rechazarKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Rechazar @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function venderKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Vender @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

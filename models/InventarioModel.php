<?php
/**
 * InventarioModel
 * 
 * Modelo que gestiona el inventario de keys (licencias) de juegos.
 * Realiza operaciones de listado, aprobación, rechazo y venta de keys
 * mediante procedimientos almacenados en el servidor remoto.
 */

require_once __DIR__ . '/DB.php';

class InventarioModel {

    /**
     * listarInventario()
     * 
     * Obtiene el listado completo del inventario de keys disponibles.
     * Invoca el procedimiento almacenado sp_Inventario_Listar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Inventario_Listar
     * 
     * @return array Lista de todas las keys con información de disponibilidad, total y vendidas
     */
    public static function listarInventario() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Inventario_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * desactivarKey()
     * 
     * Desactiva una key específica del inventario.
     * Invoca el procedimiento almacenado sp_Key_Desactivar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Key_Desactivar
     * 
     * @param int $keyId Identificador de la key a desactivar
     * @return bool true si la desactivación fue exitosa, false en caso de error
     */
    public static function desactivarKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Desactivar @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * listarPendientes()
     * 
     * Obtiene la lista de keys pendientes de revisión/aprobación.
     * Invoca el procedimiento almacenado sp_Keys_Pendientes.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Keys_Pendientes
     * 
     * @return array Lista de keys en estado PENDIENTE
     */
    public static function listarPendientes() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Keys_Pendientes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * aprobarKey()
     * 
     * Aprueba una key pendiente y la pone disponible para su venta.
     * Invoca el procedimiento almacenado sp_Key_Aprobar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Key_Aprobar
     * 
     * @param int $keyId Identificador de la key a aprobar
     * @return bool true si la aprobación fue exitosa, false en caso de error
     */
    public static function aprobarKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Aprobar @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * rechazarKey()
     * 
     * Rechaza una key pendiente y la marca como no aprobada.
     * Invoca el procedimiento almacenado sp_Key_Rechazar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Key_Rechazar
     * 
     * @param int $keyId Identificador de la key a rechazar
     * @return bool true si el rechazo fue exitoso, false en caso de error
     */
    public static function rechazarKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Rechazar @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * venderKey()
     * 
     * Marca una key como vendida en el inventario.
     * Invoca el procedimiento almacenado sp_Key_Vender.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Key_Vender
     * 
     * @param int $keyId Identificador de la key vendida
     * @return bool true si la actualización de venta fue exitosa, false en caso de error
     */
    public static function venderKey(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Key_Vender @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

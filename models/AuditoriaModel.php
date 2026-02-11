<?php
/**
 * AuditoriaModel
 * 
 * Modelo que gestiona el registro de auditoría del sistema.
 * Los eventos se almacenan en la base de datos remota mediante procedimientos almacenados.
 */

require_once __DIR__ . '/DB.php';

class AuditoriaModel
{
    /**
     * registrar()
     * 
     * Registra un evento de auditoría en la base de datos.
     * Invoca el procedimiento almacenado sp_Auditoria_Registrar en el servidor remoto
     * para guardar acciones de usuarios relacionadas con auditoría.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Auditoria_Registrar
     * 
     * @param int $usuarioId Identificador del usuario que realizó la acción
     * @param string $accion Tipo de acción realizada (LOGIN, LOGOUT, CREAR, EDITAR, BORRAR, etc)
     * @param string $modulo Módulo o tabla afectada (Usuarios, Juegos, Ordenes, etc)
     * @param string $detalle Descripción detallada del evento
     * @return bool true si el registro fue exitoso, false en caso de error
     */
    public static function registrar(int $usuarioId, string $accion, string $modulo, string $detalle): bool
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Auditoria_Registrar
                @UsuarioId = :usuarioId,
                @Accion = :accion,
                @Modulo = :modulo,
                @Detalle = :detalle
        ");
        $stmt->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':accion', $accion);
        $stmt->bindValue(':modulo', $modulo);
        $stmt->bindValue(':detalle', $detalle);
        return $stmt->execute();
    }

    /**
     * listar()
     * 
     * Obtiene el listado completo de eventos de auditoría del sistema.
     * Invoca el procedimiento almacenado sp_Auditoria_Listar para recuperar
     * todos los registros de auditoría almacenados.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Auditoria_Listar
     * 
     * @return array Lista de eventos de auditoría con datos de usuario, acción, módulo y fecha
     */
    public static function listar(): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Auditoria_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

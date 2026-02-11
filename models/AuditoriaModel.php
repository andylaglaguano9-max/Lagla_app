<?php
require_once __DIR__ . '/DB.php';

class AuditoriaModel
{
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

    public static function listar(): array
    {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Auditoria_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

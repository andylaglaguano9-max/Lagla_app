<?php
require_once __DIR__ . '/DB.php';

class PerfilModel
{
    public static function obtenerPerfil(int $usuarioId): ?array
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            SELECT UsuarioId, Tipo, Nombre, Email, Telefono
            FROM [10.26.208.149].GBB_Remoto.dbo.Usuarios
            WHERE UsuarioId = :id
        ");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function actualizarPerfil(int $usuarioId, string $nombre, string $email, string $telefono): bool
    {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuario_ActualizarPerfil
                @UsuarioId = :id,
                @Nombre = :nombre,
                @Email = :email,
                @Telefono = :telefono
        ");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':telefono', $telefono);
        return $stmt->execute();
    }
}

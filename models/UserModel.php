<?php
require_once __DIR__ . '/DB.php';

class UserModel {

    public static function listarUsuarios() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerUsuario(int $usuarioId) {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Obtener @UsuarioId = :id");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function crearUsuario(string $tipo, string $nombre, string $email, string $passwordHash, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Crear
                @Tipo = :tipo,
                @Nombre = :nombre,
                @Email = :email,
                @PasswordHash = :password,
                @Estado = :estado
        ");
        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $passwordHash);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function editarUsuario(int $usuarioId, string $tipo, string $nombre, string $email, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Editar
                @UsuarioId = :id,
                @Tipo = :tipo,
                @Nombre = :nombre,
                @Email = :email,
                @Estado = :estado
        ");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function eliminarUsuario(int $usuarioId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Eliminar @UsuarioId = :id");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function borrarUsuario(int $usuarioId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Borrar @UsuarioId = :id");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

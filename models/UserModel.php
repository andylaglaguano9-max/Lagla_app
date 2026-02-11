<?php
/**
 * UserModel
 * 
 * Modelo que gestiona operaciones CRUD de usuarios del sistema.
 * Las operaciones se realizan mediante procedimientos almacenados
 * contra la tabla Usuarios en GBB_Remoto.
 */

require_once __DIR__ . '/DB.php';

class UserModel {

    /**
     * listarUsuarios()
     * 
     * Obtiene el listado completo de usuarios del sistema.
     * Invoca el procedimiento almacenado sp_Usuarios_Listar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Listar
     * 
     * @return array Lista de todos los usuarios con sus datos
     */
    public static function listarUsuarios() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * obtenerUsuario()
     * 
     * Obtiene los datos de un usuario específico por su ID.
     * Invoca el procedimiento almacenado sp_Usuarios_Obtener.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Obtener
     * 
     * @param int $usuarioId Identificador único del usuario
     * @return array|null Datos completos del usuario o null si no existe
     */
    public static function obtenerUsuario(int $usuarioId) {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Obtener @UsuarioId = :id");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * crearUsuario()
     * 
     * Crea un nuevo usuario en el sistema.
     * Invoca el procedimiento almacenado sp_Usuarios_Crear.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Crear
     * 
     * @param string $tipo Tipo de usuario (CLIENTE, VENDEDOR, ADMIN)
     * @param string $nombre Nombre completo del usuario
     * @param string $email Correo electrónico del usuario
     * @param string $passwordHash Hash de la contraseña cifrada
     * @param int $estado Estado del usuario (1 = activo, 0 = inactivo)
     * @return bool true si la creación fue exitosa, false en caso de error
     */
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

    /**
     * editarUsuario()
     * 
     * Actualiza los datos de un usuario existente.
     * Invoca el procedimiento almacenado sp_Usuarios_Editar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Editar
     * 
     * @param int $usuarioId Identificador del usuario a actualizar
     * @param string $tipo Nuevo tipo de usuario
     * @param string $nombre Nuevo nombre
     * @param string $email Nuevo correo electrónico
     * @param int $estado Nuevo estado (1 = activo, 0 = inactivo)
     * @return bool true si la actualización fue exitosa, false en caso de error
     */
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

    /**
     * eliminarUsuario()
     * 
     * Desactiva un usuario sin eliminar sus datos (soft delete).
     * Invoca el procedimiento almacenado sp_Usuarios_Eliminar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Eliminar
     * 
     * @param int $usuarioId Identificador del usuario a desactivar
     * @return bool true si la desactivación fue exitosa, false en caso de error
     */
    public static function eliminarUsuario(int $usuarioId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Eliminar @UsuarioId = :id");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * borrarUsuario()
     * 
     * Elimina permanentemente un usuario de la base de datos (hard delete).
     * Invoca el procedimiento almacenado sp_Usuarios_Borrar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Borrar
     * 
     * @param int $usuarioId Identificador del usuario a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso de error
     */
    public static function borrarUsuario(int $usuarioId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Usuarios_Borrar @UsuarioId = :id");
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

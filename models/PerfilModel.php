<?php
/**
 * PerfilModel
 * 
 * Modelo que gestiona la información de perfil de los usuarios.
 * Realiza operaciones de lectura y actualización de datos de perfil
 * en la tabla Usuarios del servidor remoto.
 */

require_once __DIR__ . '/DB.php';

class PerfilModel
{
    /**
     * obtenerPerfil()
     * 
     * Obtiene los datos de perfil de un usuario específico.
     * Consulta la tabla Usuarios en GBB_Remoto para recuperar información
     * de nombre, email, teléfono y tipo de usuario.
     * 
     * Tabla: [10.26.208.149].GBB_Remoto.dbo.Usuarios
     * 
     * @param int $usuarioId Identificador del usuario
     * @return array|null Array con datos de perfil o null si no existe
     */
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

    /**
     * actualizarPerfil()
     * 
     * Actualiza los datos de perfil de un usuario.
     * Invoca el procedimiento almacenado sp_Usuario_ActualizarPerfil
     * para guardar los cambios de nombre, email y teléfono.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Usuario_ActualizarPerfil
     * 
     * @param int $usuarioId Identificador del usuario
     * @param string $nombre Nuevo nombre del usuario
     * @param string $email Nuevo correo electrónico
     * @param string $telefono Nuevo número de teléfono
     * @return bool true si la actualización fue exitosa, false en caso de error
     */
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

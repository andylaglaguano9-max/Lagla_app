<?php
/**
 * AuthModel
 * 
 * Modelo que gestiona la autenticación de usuarios.
 * Realiza consultas directas contra la tabla de usuarios en el servidor remoto
 * y valida credenciales mediante hash de contraseña.
 */

require_once __DIR__ . '/DB.php';

class AuthModel {

    /**
     * login()
     * 
     * Autentica un usuario consultando la tabla Usuarios en GBB_Remoto.
     * Busca el usuario por correo electrónico en estado activo (Estado = 1),
     * luego verifica la contraseña proporcionada contra el hash almacenado.
     * 
     * Base de datos remota: [10.26.208.149].GBB_Remoto.dbo.Usuarios
     * 
     * @param string $email Correo electrónico del usuario
     * @param string $password Contraseña sin cifrar proporcionada por el usuario
     * @return array|false Array con datos del usuario si la autenticación es exitosa, false si falla
     */
    public static function login($email, $password) {

        $db = DB::conn();

        // Consulta a USUARIOS EN GBB_REMOTO (LINKED SERVER 10.26.208.149)
        $sql = "
            SELECT UsuarioId, Tipo, Nombre, Email, PasswordHash
            FROM [10.26.208.149].GBB_Remoto.dbo.Usuarios
            WHERE Email = ? AND Estado = 1
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        // Verifica la contraseña cifrada utilizando PASSWORD_DEFAULT de PHP
        if (!password_verify($password, $user['PasswordHash'])) {
            return false;
        }

        return $user;
    }
}

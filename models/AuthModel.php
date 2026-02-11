<?php
require_once __DIR__ . '/DB.php';

class AuthModel {

    public static function login($email, $password) {

        $db = DB::conn();

        //  USUARIOS DESDE GBB_REMOTO (LINKED SERVER)
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

        //  Verificar contraseña cifrada
        if (!password_verify($password, $user['PasswordHash'])) {
            return false;
        }

        return $user;
    }
}

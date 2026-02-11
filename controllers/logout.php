<?php
declare(strict_types=1);

/**
 * logout.php
 * 
 * Controlador que gestiona el cierre de sesión del usuario.
 * Registra el evento de logout en auditoría y destruye la sesión.
 */

session_start();

require_once __DIR__ . '/../models/AuditoriaModel.php';

// Intenta registrar el evento de logout en auditoría
try {
    $uid = (int)($_SESSION['auth']['UsuarioId'] ?? 0);
    if ($uid > 0) {
        AuditoriaModel::registrar($uid, 'LOGOUT', 'Usuarios', 'Cierre de sesión');
    }
} catch (Exception $e) {
    // No bloquea el logout si falla el registro de auditoría
}

// Destruye toda la sesión del usuario
session_destroy();

// Redirige a la página de login después de cerrar sesión
header("Location: login.php");
exit;

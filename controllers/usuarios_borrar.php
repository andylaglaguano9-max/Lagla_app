<?php
declare(strict_types=1);

/**
 * usuarios_borrar.php
 * 
 * Controlador que procesa la eliminación (borrado) de un usuario.
 * Valida el ID del usuario, lo elimina de la base de datos,
 * registra el evento en auditoría y redirige al listado.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/usuarios.php");
    exit;
}

// Obtiene el ID del usuario a eliminar
$usuarioId = (int)($_POST['usuarioId'] ?? 0);

// Valida que el ID sea válido
if ($usuarioId <= 0) {
    $_SESSION['flash_error'] = 'Usuario inválido';
    header("Location: admin/usuarios.php");
    exit;
}

try {
    // Elimina el usuario de la base de datos
    UserModel::borrarUsuario($usuarioId);
    
    // Registra el evento de eliminación en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'BORRAR',
            'Usuarios',
            "Usuario eliminado: {$usuarioId}"
        );
    } catch (Exception $e) {
        // No bloquea la eliminación si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Usuario eliminado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

// Redirige al listado de usuarios
header("Location: admin/usuarios.php");
exit;

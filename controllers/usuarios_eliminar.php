<?php
declare(strict_types=1);

/**
 * usuarios_eliminar.php
 * 
 * Controlador que procesa la desactivación de un usuario.
 * Valida el ID del usuario, lo desactiva en la base de datos,
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

// Obtiene el ID del usuario a desactivar
$usuarioId = (int)($_POST['usuarioId'] ?? 0);

// Valida que el ID sea válido
if ($usuarioId <= 0) {
    $_SESSION['flash_error'] = 'Usuario inválido';
    header("Location: admin/usuarios.php");
    exit;
}

try {
    // Desactiva el usuario en la base de datos
    UserModel::eliminarUsuario($usuarioId);
    
    // Registra el evento de desactivación en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'DESACTIVAR',
            'Usuarios',
            "Usuario desactivado: {$usuarioId}"
        );
    } catch (Exception $e) {
        // No bloquea la desactivación si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Usuario desactivado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

// Redirige al listado de usuarios
header("Location: admin/usuarios.php");
exit;

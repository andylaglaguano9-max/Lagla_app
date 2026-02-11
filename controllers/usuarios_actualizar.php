<?php
declare(strict_types=1);

/**
 * usuarios_actualizar.php
 * 
 * Controlador que procesa la actualización de datos de un usuario.
 * Valida los nuevos datos, actualiza la base de datos, registra el evento
 * en auditoría y redirige al listado de usuarios.
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

// Obtiene y limpia los datos del formulario
$usuarioId = (int)($_POST['usuarioId'] ?? 0);
$tipo = trim($_POST['tipo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$estado = isset($_POST['estado']) ? 1 : 0;

// Valida que los campos obligatorios sean válidos
if ($usuarioId <= 0 || $tipo === '' || $nombre === '' || $email === '') {
    $_SESSION['flash_error'] = 'Datos inválidos';
    header("Location: usuarios_editar.php?id=" . $usuarioId);
    exit;
}

try {
    // Actualiza el usuario con los nuevos datos
    UserModel::editarUsuario($usuarioId, $tipo, $nombre, $email, $estado);
    
    // Registra el evento de actualización en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'EDITAR',
            'Usuarios',
            "Usuario actualizado: {$usuarioId} ({$email})"
        );
    } catch (Exception $e) {
        // No bloquea la actualización si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Usuario actualizado';
    header("Location: admin/usuarios.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: usuarios_editar.php?id=" . $usuarioId);
    exit;
}

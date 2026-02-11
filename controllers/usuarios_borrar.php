<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/usuarios.php");
    exit;
}

$usuarioId = (int)($_POST['usuarioId'] ?? 0);
if ($usuarioId <= 0) {
    $_SESSION['flash_error'] = 'Usuario inválido';
    header("Location: admin/usuarios.php");
    exit;
}

try {
    UserModel::borrarUsuario($usuarioId);
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'BORRAR',
            'Usuarios',
            "Usuario eliminado: {$usuarioId}"
        );
    } catch (Exception $e) {
        // No bloquear eliminación si falla auditoría
    }
    $_SESSION['flash_success'] = 'Usuario eliminado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

header("Location: admin/usuarios.php");
exit;

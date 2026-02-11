<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../../models/UserModel.php';

$error = null;

if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $usuarioId = (int)$_GET['eliminar'];
    try {
        UserModel::eliminarUsuario($usuarioId);
        $_SESSION['flash_success'] = 'Usuario desactivado';
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header("Location: usuarios.php");
    exit;
}

$usuarios = [];

try {
    $usuarios = UserModel::listarUsuarios();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/usuarios/index.php';

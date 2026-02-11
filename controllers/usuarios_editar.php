<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../models/UserModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin/usuarios.php");
    exit;
}

$usuarioId = (int)$_GET['id'];
$usuario = UserModel::obtenerUsuario($usuarioId);

if (!$usuario) {
    $_SESSION['flash_error'] = 'Usuario no encontrado';
    header("Location: admin/usuarios.php");
    exit;
}

require_once __DIR__ . '/../views/usuarios/editar.php';

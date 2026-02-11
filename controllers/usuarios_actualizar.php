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
$tipo = trim($_POST['tipo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$estado = isset($_POST['estado']) ? 1 : 0;

if ($usuarioId <= 0 || $tipo === '' || $nombre === '' || $email === '') {
    $_SESSION['flash_error'] = 'Datos inválidos';
    header("Location: usuarios_editar.php?id=" . $usuarioId);
    exit;
}

try {
    UserModel::editarUsuario($usuarioId, $tipo, $nombre, $email, $estado);
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'EDITAR',
            'Usuarios',
            "Usuario actualizado: {$usuarioId} ({$email})"
        );
    } catch (Exception $e) {
        // No bloquear actualización si falla auditoría
    }
    $_SESSION['flash_success'] = 'Usuario actualizado';
    header("Location: admin/usuarios.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: usuarios_editar.php?id=" . $usuarioId);
    exit;
}

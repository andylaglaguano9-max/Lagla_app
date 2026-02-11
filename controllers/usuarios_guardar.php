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

$tipo = trim($_POST['tipo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$estado = isset($_POST['estado']) ? 1 : 0;

if ($tipo === '' || $nombre === '' || $email === '' || $password === '') {
    $_SESSION['flash_error'] = 'Completa todos los campos';
    header("Location: usuarios_crear.php");
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    UserModel::crearUsuario($tipo, $nombre, $email, $hash, $estado);
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'CREAR',
            'Usuarios',
            "Usuario creado: {$nombre} ({$email})"
        );
    } catch (Exception $e) {
        // No bloquear creación si falla auditoría
    }
    $_SESSION['flash_success'] = 'Usuario creado';
    header("Location: admin/usuarios.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: usuarios_crear.php");
    exit;
}

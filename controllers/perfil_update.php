<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireLogin();
require_once __DIR__ . '/../models/PerfilModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

$rol = strtoupper(trim((string)($_SESSION['auth']['Tipo'] ?? '')));
$redirect = ($rol === 'VENDEDOR') ? 'vendedor.php' : 'perfil.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$redirect}");
    exit;
}

$usuarioId = (int)($_SESSION['auth']['UsuarioId'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if ($usuarioId <= 0 || $nombre === '' || $email === '') {
    $_SESSION['flash_error'] = 'Completa nombre y correo';
    header("Location: {$redirect}");
    exit;
}

try {
    PerfilModel::actualizarPerfil($usuarioId, $nombre, $email, $telefono);
    $_SESSION['auth']['Nombre'] = $nombre;
    $_SESSION['auth']['Email'] = $email;
    $_SESSION['auth']['Telefono'] = $telefono;
    try {
        AuditoriaModel::registrar($usuarioId, 'ACTUALIZAR', 'Perfil', 'Actualizó datos de perfil');
    } catch (Exception $e) {
    }
    $_SESSION['flash_success'] = 'Perfil actualizado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

header("Location: {$redirect}");
exit;

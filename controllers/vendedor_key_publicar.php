<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: vendedor_publicar.php");
    exit;
}

$auth = $_SESSION['auth'] ?? [];
$vendedorId = (int)($auth['UsuarioId'] ?? 0);
$juegoId = (int)($_POST['juegoId'] ?? 0);
$keyValor = trim($_POST['keyValor'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);

if ($vendedorId <= 0 || $juegoId <= 0 || $keyValor === '' || $precio <= 0) {
    $_SESSION['flash_error'] = 'Completa los campos obligatorios para publicar.';
    header("Location: vendedor_publicar.php");
    exit;
}

try {
    VendedorPerfilModel::publicarKey($juegoId, $vendedorId, $keyValor, $precio);
    try {
        AuditoriaModel::registrar($vendedorId, 'PUBLICAR', 'Keys', "Publicó una key para el juego ID {$juegoId}");
    } catch (Exception $e) {
        // No bloquear publicación si falla auditoría
    }
    $_SESSION['flash_success'] = 'Key publicada en estado PENDIENTE.';
    header("Location: vendedor_publicar.php");
    exit;
} catch (Exception $e) {
    $msg = $e->getMessage();
    if (stripos($msg, 'UNIQUE') !== false || stripos($msg, 'duplicate') !== false) {
        $_SESSION['flash_error'] = 'Esta key ya existe. Usa una clave diferente.';
    } else {
        $_SESSION['flash_error'] = 'No se pudo publicar la key. Verifica los datos e intenta de nuevo.';
    }
    header("Location: vendedor_publicar.php");
    exit;
}

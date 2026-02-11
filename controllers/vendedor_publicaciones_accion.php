<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: vendedor_publicaciones.php");
    exit;
}

$auth = $_SESSION['auth'] ?? [];
$vendedorId = (int)($auth['UsuarioId'] ?? 0);
$accion = $_POST['accion'] ?? '';
$keyId = (int)($_POST['keyId'] ?? 0);

if ($vendedorId <= 0 || $keyId <= 0) {
    $_SESSION['flash_error'] = 'Acción no válida.';
    header("Location: vendedor_publicaciones.php");
    exit;
}

try {
    if ($accion === 'actualizar') {
        $keyValor = trim($_POST['keyValor'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        if ($keyValor === '' || $precio <= 0) {
            $_SESSION['flash_error'] = 'Completa key y precio.';
        } else {
            VendedorPerfilModel::actualizarPendiente($keyId, $vendedorId, $keyValor, $precio);
            $_SESSION['flash_success'] = 'Key pendiente actualizada.';
        }
    } elseif ($accion === 'eliminar') {
        VendedorPerfilModel::eliminarPendiente($keyId, $vendedorId);
        $_SESSION['flash_success'] = 'Key pendiente eliminada.';
    } else {
        $_SESSION['flash_error'] = 'Acción no válida.';
    }
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

header("Location: vendedor_publicaciones.php");
exit;


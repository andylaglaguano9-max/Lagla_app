<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$vendedorKeys = [];
$error = null;

try {
    $vendedorKeys = VendedorPerfilModel::misKeys($usuarioId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../views/vendedor/publicaciones.php';


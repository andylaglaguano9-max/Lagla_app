<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$vendedorVentas = [];
$error = null;

try {
    $vendedorVentas = VendedorPerfilModel::misVentas($usuarioId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../views/vendedor/ventas.php';


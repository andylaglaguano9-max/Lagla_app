<?php
declare(strict_types=1);

/**
 * vendedor_ventas.php
 * 
 * Controlador que renderiza el historial de ventas del vendedor.
 * Obtiene todas las ventas realizadas por el vendedor autenticado.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

// Obtiene los datos de autenticación del vendedor
$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$vendedorVentas = [];
$error = null;

// Intenta obtener el historial de ventas del vendedor
try {
    $vendedorVentas = VendedorPerfilModel::misVentas($usuarioId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Carga la vista con el historial de ventas
require_once __DIR__ . '/../views/vendedor/ventas.php';


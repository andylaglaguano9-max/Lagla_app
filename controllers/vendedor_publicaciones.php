<?php
declare(strict_types=1);

/**
 * vendedor_publicaciones.php
 * 
 * Controlador que renderiza la lista de publicaciones (keys) del vendedor.
 * Obtiene todas las keys publicadas por el vendedor autenticado.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

// Obtiene los datos de autenticación del vendedor
$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$vendedorKeys = [];
$error = null;

// Intenta obtener todas las keys publicadas por el vendedor
try {
    $vendedorKeys = VendedorPerfilModel::misKeys($usuarioId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Carga la vista con el listado de publicaciones
require_once __DIR__ . '/../views/vendedor/publicaciones.php';


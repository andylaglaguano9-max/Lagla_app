<?php
declare(strict_types=1);

/**
 * vendedor.php
 * 
 * Controlador del panel de vendedor.
 * Valida que el usuario tenga rol de vendedor y renderiza su panel principal.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

// Carga la vista del panel de vendedor
require_once __DIR__ . '/../views/vendedor/index.php';

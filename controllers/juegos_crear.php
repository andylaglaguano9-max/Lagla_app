<?php
declare(strict_types=1);

/**
 * juegos_crear.php
 * 
 * Controlador que renderiza el formulario de creación de nuevos juegos.
 * Solo administradores pueden acceder a esta funcionalidad.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

// Carga la vista del formulario de creación de juegos
require_once __DIR__ . '/../views/admin/juegos_crear.php';

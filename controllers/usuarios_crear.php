<?php
declare(strict_types=1);

/**
 * usuarios_crear.php
 * 
 * Controlador que renderiza el formulario de creación de nuevos usuarios.
 * Solo administradores pueden acceder a esta funcionalidad.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

// Carga la vista del formulario de creación de usuarios
require_once __DIR__ . '/../views/usuarios/crear.php';

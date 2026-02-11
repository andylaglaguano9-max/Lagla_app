<?php
declare(strict_types=1);

/**
 * login.php
 * 
 * Controlador que renderiza el formulario de acceso a la aplicación.
 * Obtiene mensajes de error de intentos fallidos de login y los muestra
 * en la vista antes de limpiar la sesión.
 */

session_start();

// Obtiene el mensaje de error del intento de login anterior, si existe
$error = $_SESSION['login_error'] ?? null;

// Limpia el mensaje de error después de leerlo para evitar duplicados
unset($_SESSION['login_error']);

// Renderiza la vista del formulario de login
require_once __DIR__ . '/../views/auth/login.php';

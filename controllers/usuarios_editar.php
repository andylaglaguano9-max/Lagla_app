<?php
declare(strict_types=1);

/**
 * usuarios_editar.php
 * 
 * Controlador que renderiza el formulario de edición de un usuario existente.
 * Obtiene la información del usuario y la carga en el formulario para su modificación.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

require_once __DIR__ . '/../models/UserModel.php';

// Valida que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin/usuarios.php");
    exit;
}

// Obtiene el ID del usuario a editar
$usuarioId = (int)$_GET['id'];

// Obtiene la información actual del usuario desde la base de datos
$usuario = UserModel::obtenerUsuario($usuarioId);

// Valida que el usuario exista
if (!$usuario) {
    $_SESSION['flash_error'] = 'Usuario no encontrado';
    header("Location: admin/usuarios.php");
    exit;
}

// Carga la vista del formulario de edición con los datos del usuario
require_once __DIR__ . '/../views/usuarios/editar.php';

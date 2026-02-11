<?php
declare(strict_types=1);

/**
 * juegos_editar.php
 * 
 * Controlador que renderiza el formulario de edición de un juego existente.
 * Obtiene la información del juego y la carga en el formulario para su modificación.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

require_once __DIR__ . '/../models/GameModel.php';

// Valida que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin/admin_juegos.php");
    exit;
}

// Obtiene el ID del juego a editar
$juegoId = (int)$_GET['id'];

// Obtiene la información actual del juego desde la base de datos
$juego = GameModel::obtenerJuego($juegoId);

// Valida que el juego exista
if (!$juego) {
    $_SESSION['flash_error'] = 'Juego no encontrado';
    header("Location: admin/admin_juegos.php");
    exit;
}

// Carga la vista del formulario de edición con los datos del juego
require_once __DIR__ . '/../views/admin/juegos_editar.php';

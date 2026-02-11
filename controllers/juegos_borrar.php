<?php
declare(strict_types=1);

/**
 * juegos_borrar.php
 * 
 * Controlador que procesa la eliminación de un juego.
 * Valida el ID del juego, lo elimina de la base de datos,
 * registra el evento en auditoría y redirige al listado.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admin_juegos.php");
    exit;
}

// Obtiene el ID del juego a eliminar
$juegoId = (int)($_POST['juegoId'] ?? 0);

// Valida que el ID sea válido
if ($juegoId <= 0) {
    $_SESSION['flash_error'] = 'Juego inválido';
    header("Location: admin/admin_juegos.php");
    exit;
}

try {
    // Elimina el juego de la base de datos
    GameModel::borrarJuego($juegoId);
    
    // Registra el evento de eliminación en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'BORRAR',
            'Juegos',
            "Juego eliminado: {$juegoId}"
        );
    } catch (Exception $e) {
        // No bloquea la eliminación si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Juego eliminado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

// Redirige al listado de juegos
header("Location: admin/admin_juegos.php");
exit;

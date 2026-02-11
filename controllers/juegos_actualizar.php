<?php
declare(strict_types=1);

/**
 * juegos_actualizar.php
 * 
 * Controlador que procesa la actualización de datos de un juego.
 * Valida los nuevos datos, actualiza la base de datos, registra el evento
 * en auditoría y redirige hacía el listado de juegos.
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

// Obtiene y limpia los datos del formulario
$juegoId = (int)($_POST['juegoId'] ?? 0);
$plataformaId = (int)($_POST['plataformaId'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$genero = trim($_POST['genero'] ?? '');
$imagenUrl = trim($_POST['imagenUrl'] ?? '');
$estado = isset($_POST['estado']) ? 1 : 0;

// Valida que los campos obligatorios sean válidos
if ($juegoId <= 0 || $plataformaId <= 0 || $nombre === '' || $precio <= 0 || $genero === '') {
    $_SESSION['flash_error'] = 'Datos inválidos';
    header("Location: juegos_editar.php?id=" . $juegoId);
    exit;
}

try {
    // Actualiza el juego con los nuevos datos
    GameModel::editarJuego($juegoId, $plataformaId, $nombre, $descripcion, $precio, $genero, $imagenUrl, $estado);
    
    // Registra el evento de actualización en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'EDITAR',
            'Juegos',
            "Juego actualizado: {$juegoId} ({$nombre})"
        );
    } catch (Exception $e) {
        // No bloquea la actualización si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Juego actualizado';
    header("Location: admin/admin_juegos.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: juegos_editar.php?id=" . $juegoId);
    exit;
}

<?php
declare(strict_types=1);

/**
 * juegos_guardar.php
 * 
 * Controlador que procesa la creación de un nuevo juego.
 * Valida los datos proporcionados, guarda el juego en la base de datos,
 * registra el evento en auditoría y redirige al listado de juegos.
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
$plataformaId = (int)($_POST['plataformaId'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$genero = trim($_POST['genero'] ?? '');
$imagenUrl = trim($_POST['imagenUrl'] ?? '');

// Valida que los campos obligatorios estén completos
if ($plataformaId <= 0 || $nombre === '' || $precio <= 0 || $genero === '') {
    $_SESSION['flash_error'] = 'Completa los campos obligatorios';
    header("Location: juegos_crear.php");
    exit;
}

try {
    // Crea el nuevo juego en la base de datos
    GameModel::crearJuego($plataformaId, $nombre, $descripcion, $precio, $genero, $imagenUrl);
    
    // Registra el evento de creación en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'AGREGAR',
            'Juegos',
            "Juego agregado: {$nombre}"
        );
    } catch (Exception $e) {
        // No bloquea la creación si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Juego creado';
    header("Location: admin/admin_juegos.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: juegos_crear.php");
    exit;
}

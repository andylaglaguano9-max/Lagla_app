<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admin_juegos.php");
    exit;
}

$plataformaId = (int)($_POST['plataformaId'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);
$genero = trim($_POST['genero'] ?? '');
$imagenUrl = trim($_POST['imagenUrl'] ?? '');

if ($plataformaId <= 0 || $nombre === '' || $precio <= 0 || $genero === '') {
    $_SESSION['flash_error'] = 'Completa los campos obligatorios';
    header("Location: juegos_crear.php");
    exit;
}

try {
    GameModel::crearJuego($plataformaId, $nombre, $descripcion, $precio, $genero, $imagenUrl);
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'AGREGAR',
            'Juegos',
            "Juego agregado: {$nombre}"
        );
    } catch (Exception $e) {
        // No bloquear creación si falla auditoría
    }
    $_SESSION['flash_success'] = 'Juego creado';
    header("Location: admin/admin_juegos.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: juegos_crear.php");
    exit;
}

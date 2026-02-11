<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../models/GameModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin/admin_juegos.php");
    exit;
}

$juegoId = (int)$_GET['id'];
$juego = GameModel::obtenerJuego($juegoId);

if (!$juego) {
    $_SESSION['flash_error'] = 'Juego no encontrado';
    header("Location: admin/admin_juegos.php");
    exit;
}

require_once __DIR__ . '/../views/admin/juegos_editar.php';

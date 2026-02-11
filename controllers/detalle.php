<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../models/CatalogModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Juego no válido');
}

$juegoId = (int) $_GET['id'];

$juego = CatalogModel::obtenerDetalleJuego($juegoId);

if (!$juego) {
    die('Juego no encontrado');
}

require_once __DIR__ . '/../views/catalog/detalle.php';

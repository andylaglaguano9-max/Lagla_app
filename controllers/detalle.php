<?php
declare(strict_types=1);

/**
 * detalle.php
 * 
 * Controlador que renderiza la vista de detalle de un juego específico.
 * Obtiene el ID del juego desde los parámetros GET, valida su existencia
 * y carga la información completa del producto.
 */

session_start();

require_once __DIR__ . '/../models/CatalogModel.php';

// Valida que se haya proporcionado un ID válido en la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Juego no válido');
}

// Obtiene y convierte el ID del juego a entero
$juegoId = (int) $_GET['id'];

// Obtiene los detalles del juego desde el modelo
$juego = CatalogModel::obtenerDetalleJuego($juegoId);

// Valida que el juego exista en la base de datos
if (!$juego) {
    die('Juego no encontrado');
}

// Renderiza la vista con los detalles del juego
require_once __DIR__ . '/../views/catalog/detalle.php';

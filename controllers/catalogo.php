<?php
declare(strict_types=1);

/**
 * catalogo.php
 * 
 * Controlador que renderiza el catálogo de juegos disponibles.
 * Valida la autenticación del usuario, obtiene la lista de juegos,
 * cuenta los artículos en el carrito y gestiona mensajes de estado.
 */

session_start();

// Verifica que el usuario esté autenticado
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../models/CatalogModel.php';

// Obtiene la lista completa de juegos disponibles en el catálogo
$juegos = CatalogModel::listarJuegos();

// Calcula la cantidad total de items en el carrito del usuario
$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}

// Obtiene mensajes de error relacionados con la disponibilidad de base de datos
$error = $_SESSION['db_offline_message'] ?? null;

// Limpia el mensaje de sesión después de leerlo para evitar visualizaciones repetidas
if (isset($_SESSION['db_offline_message'])) unset($_SESSION['db_offline_message']);

// Carga la vista del catálogo con los juegos y datos del carrito
require_once __DIR__ . '/../views/catalog/index.php';

<?php
declare(strict_types=1);

/**
 * home.php
 * 
 * Controlador de la página de inicio de la aplicación.
 * Obtiene el catálogo de juegos, cuenta los items del carrito
 * y maneja mensajes de error de disponibilidad de base de datos.
 */

session_start();

/* ============================
   Validación de sesión
============================ */
// Verifica que el usuario esté autenticado
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

/* ============================
   Carga del modelo
============================ */
require_once __DIR__ . '/../models/CatalogModel.php';

/* ============================
   Datos del usuario autenticado
============================ */
// Obtiene la información del usuario desde la sesión
$auth = $_SESSION['auth'] ?? [];

// Calcula la cantidad total de items en el carrito
$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}

/* ============================
   Obtención del catálogo
============================ */
// Obtiene la lista completa de juegos disponibles
$juegos = CatalogModel::listarJuegos();

/* ============================
   Manejo de mensajes de error
============================ */
// Obtiene mensajes de error relacionados con disponibilidad de base de datos
$error = $_SESSION['db_offline_message'] ?? null;

// Limpia el mensaje después de leerlo para evitar visualizaciones repetidas
if (isset($_SESSION['db_offline_message'])) {
    unset($_SESSION['db_offline_message']);
}

/* ============================
   Carga de la vista
============================ */
// Renderiza la vista de inicio con todos los datos obtenidos
require_once __DIR__ . '/../views/home/index.php';

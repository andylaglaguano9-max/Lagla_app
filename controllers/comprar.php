<?php
/**
 * comprar.php
 * 
 * Controlador de acción rápida para agregar un juego al carrito desde la página de catálogo.
 * Obtiene el ID del juego, valida su es válido y lo agrega al carrito de sesión.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE', 'ADMIN']);

// Obtiene el ID del juego desde los parámetros GET
$juegoId = intval($_GET['id'] ?? 0);

// Valida que el ID sea válido
if ($juegoId <= 0) {
    $_SESSION['flash_error'] = 'Juego inválido.';
    header("Location: catalogo.php");
    exit;
}

// Inicializa el carrito si no existe en la sesión
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Incrementa la cantidad del juego en el carrito
$_SESSION['cart'][$juegoId] = ($_SESSION['cart'][$juegoId] ?? 0) + 1;

// Establece mensaje de confirmación
$_SESSION['flash_success'] = 'Juego agregado al carrito.';

// Redirige al carrito
header("Location: carrito.php");
exit;

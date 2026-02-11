<?php
declare(strict_types=1);

/**
 * carrito_agregar.php
 * 
 * Controlador para agregar juegos al carrito de compras.
 * Obtiene el ID del juego del formulario o query string, valida que sea válido,
 * e incrementa la cantidad en el carrito de sesión del usuario.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

// Obtiene el ID del juego desde POST o GET, con validación de tipo
$juegoId = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

// Valida que el ID sea un valor válido
if ($juegoId <= 0) {
    $_SESSION['flash_error'] = 'Juego inválido.';
    header("Location: catalogo.php");
    exit;
}

// Inicializa el carrito si no existe en la sesión
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Incrementa la cantidad del juego en el carrito (o la crea si no existe)
$_SESSION['cart'][$juegoId] = ($_SESSION['cart'][$juegoId] ?? 0) + 1;

// Establece mensaje de confirmación para mostrar al usuario
$_SESSION['flash_success'] = 'Juego agregado al carrito.';

// Redirige al carrito para que el usuario vea su compra actualizada
header("Location: carrito.php");
exit;


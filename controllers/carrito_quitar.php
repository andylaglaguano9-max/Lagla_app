<?php
declare(strict_types=1);

/**
 * carrito_quitar.php
 * 
 * Controlador para remover juegos del carrito de compras.
 * Valida el ID del juego y lo elimina del carrito de sesión si existe.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

// Obtiene el ID del juego a remover desde POST, con validación de tipo
$juegoId = (int)($_POST['id'] ?? 0);

// Verifica que el ID sea válido y que el juego exista en el carrito
if ($juegoId > 0 && !empty($_SESSION['cart'][$juegoId])) {
    // Elimina el juego del arreglo del carrito
    unset($_SESSION['cart'][$juegoId]);
}

// Redirige al carrito para mostrar el estado actualizado
header("Location: carrito.php");
exit;


<?php
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE', 'ADMIN']);

$juegoId = intval($_GET['id'] ?? 0);
if ($juegoId <= 0) {
    $_SESSION['flash_error'] = 'Juego inválido.';
    header("Location: catalogo.php");
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$_SESSION['cart'][$juegoId] = ($_SESSION['cart'][$juegoId] ?? 0) + 1;
$_SESSION['flash_success'] = 'Juego agregado al carrito.';
header("Location: carrito.php");
exit;

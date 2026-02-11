<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$juegoId = (int)($_POST['JuegoId'] ?? $_GET['JuegoId'] ?? 0);
if ($juegoId <= 0) {
    $_SESSION['flash_error'] = 'Juego inválido.';
    header("Location: catalogo.php");
    exit;
}

$_SESSION['cart'][$juegoId] = ($_SESSION['cart'][$juegoId] ?? 0) + 1;
$_SESSION['flash_success'] = 'Juego agregado al carrito.';
header("Location: carrito.php");
exit;

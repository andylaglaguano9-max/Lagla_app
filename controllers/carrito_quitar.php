<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

$juegoId = (int)($_POST['id'] ?? 0);
if ($juegoId > 0 && !empty($_SESSION['cart'][$juegoId])) {
    unset($_SESSION['cart'][$juegoId]);
}

header("Location: carrito.php");
exit;


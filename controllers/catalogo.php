<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../models/CatalogModel.php';
$juegos = CatalogModel::listarJuegos();
$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}
$error = $_SESSION['db_offline_message'] ?? null;
if (isset($_SESSION['db_offline_message'])) unset($_SESSION['db_offline_message']);

require_once __DIR__ . '/../views/catalog/index.php';

<?php
declare(strict_types=1);

session_start();

/* ============================
   Validar sesión
============================ */
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

/* ============================
   Cargar modelo
============================ */
require_once __DIR__ . '/../models/CatalogModel.php';

/* ============================
   Usuario autenticado
============================ */
$auth = $_SESSION['auth'] ?? [];
$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}

/* ============================
   Obtener catálogo desde SP_UI
============================ */
$juegos = CatalogModel::listarJuegos();

/* ============================
   Mensaje de error DB (si existe)
============================ */
$error = $_SESSION['db_offline_message'] ?? null;
if (isset($_SESSION['db_offline_message'])) {
    unset($_SESSION['db_offline_message']);
}

/* ============================
   Cargar vista
============================ */
require_once __DIR__ . '/../views/home/index.php';

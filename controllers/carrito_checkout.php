<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../models/OrdenesModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';
require_once __DIR__ . '/../models/CatalogModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: carrito.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['flash_error'] = 'Tu carrito estÃ¡ vacÃ­o.';
    header("Location: carrito.php");
    exit;
}

$clienteId = (int)($_SESSION['auth']['UsuarioId'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$metodo = trim($_POST['metodo'] ?? '');

if ($nombre === '' || $email === '' || $metodo === '') {
    $_SESSION['flash_error'] = 'Completa los datos de compra.';
    header("Location: carrito.php");
    exit;
}

$items = [];
$map = [];
try {
    $juegos = CatalogModel::listarJuegos();
    foreach ($juegos as $j) {
        $jid = (int)($j['JuegoId'] ?? 0);
        if ($jid > 0) {
            $map[$jid] = $j;
        }
    }
} catch (Exception $e) {
}

foreach ($cart as $juegoId => $qty) {
    $juegoId = (int)$juegoId;
    if ($juegoId <= 0) continue;
    $precio = (float)($map[$juegoId]['Precio'] ?? 0);
    $nombreJuego = $map[$juegoId]['Nombre'] ?? ("Juego {$juegoId}");
    $items[] = [
        'JuegoId' => $juegoId,
        'Nombre' => $nombreJuego,
        'Precio' => $precio,
        'Cantidad' => (int)$qty,
    ];
}

$res = OrdenesModel::confirmarCompra($clienteId, $items);
if (($res['Status'] ?? '') !== 'OK') {
    $_SESSION['flash_error'] = $res['Message'] ?? 'No se pudo confirmar la compra.';
    header("Location: carrito.php");
    exit;
}

if (!empty($res['Detalle'])) {
    $orderKey = (string)($res['Detalle']['OrdenId'] ?? '');
    $existing = $_SESSION['order_details'] ?? [];
    if (!is_array($existing)) {
        $existing = [];
    }
    $existing[$orderKey] = [
        'fecha' => $res['Detalle']['Fecha'] ?? date('Y-m-d H:i'),
        'estado' => $res['Detalle']['Estado'] ?? 'PAGADA',
        'items' => $res['Detalle']['Items'] ?? [],
        'total' => $res['Detalle']['Total'] ?? 0,
    ];
    $_SESSION['order_details'] = $existing;
}

foreach ($items as $it) {
    try {
        AuditoriaModel::registrar(
            (int)$clienteId,
            'COMPRAR',
            'Ordenes',
            "Compra de juego: {$it['JuegoId']}"
        );
    } catch (Exception $e) {
    }
}

$_SESSION['cart'] = [];
$_SESSION['flash_success'] = 'Compra realizada. Revisa tus órdenes.';
header("Location: ordenes.php");
exit;



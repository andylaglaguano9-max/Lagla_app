<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../models/CatalogModel.php';

$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;

if (!empty($cart)) {
    $juegos = CatalogModel::listarJuegos();
    $map = [];
    foreach ($juegos as $j) {
        $jid = (int)($j['JuegoId'] ?? 0);
        if ($jid > 0) {
            $map[$jid] = $j;
        }
    }
    foreach ($cart as $jid => $qty) {
        if (!isset($map[$jid])) continue;
        $row = $map[$jid];
        $precio = (float)($row['Precio'] ?? 0);
        $items[] = [
            'JuegoId' => $jid,
            'Nombre' => $row['Nombre'] ?? 'Juego',
            'Plataforma' => $row['Plataforma'] ?? '',
            'Precio' => $precio,
            'Cantidad' => (int)$qty,
        ];
        $total += $precio * (int)$qty;
    }
}

require_once __DIR__ . '/../views/carrito/index.php';


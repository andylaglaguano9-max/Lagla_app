<?php
declare(strict_types=1);

/**
 * carrito.php
 * 
 * Controlador que gestiona la visualización del carrito de compras.
 * Obtiene los artículos del carrito de la sesión, los relaciona con
 * información de juegos en la base de datos, y calcula el total de la compra.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../models/CatalogModel.php';

// Obtiene el carrito de la sesión, que es un arreglo de juegoId => cantidad
$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0.0;

if (!empty($cart)) {
    // Obtiene la lista completa de juegos disponibles
    $juegos = CatalogModel::listarJuegos();
    
    // Crea un mapa indexado por ID de juego para búsquedas rápidas
    $map = [];
    foreach ($juegos as $j) {
        $jid = (int)($j['JuegoId'] ?? 0);
        if ($jid > 0) {
            $map[$jid] = $j;
        }
    }
    
    // Itera sobre los items del carrito y prepara los datos para la vista
    foreach ($cart as $jid => $qty) {
        // Salta items sin información disponible en el catálogo
        if (!isset($map[$jid])) continue;
        
        $row = $map[$jid];
        $precio = (float)($row['Precio'] ?? 0);
        
        // Construye el item con información completa del juego
        $items[] = [
            'JuegoId' => $jid,
            'Nombre' => $row['Nombre'] ?? 'Juego',
            'Plataforma' => $row['Plataforma'] ?? '',
            'Precio' => $precio,
            'Cantidad' => (int)$qty,
        ];
        
        // Acumula el total de la compra
        $total += $precio * (int)$qty;
    }
}

// Carga la vista del carrito con los items y total calculados
require_once __DIR__ . '/../views/carrito/index.php';


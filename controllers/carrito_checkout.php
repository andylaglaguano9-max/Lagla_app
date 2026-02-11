<?php
declare(strict_types=1);

/**
 * carrito_checkout.php
 * 
 * Controlador del proceso de confirmación de compra.
 * Valida los datos de la compra, procesa la orden, registra el evento
 * en auditoría y limpia el carrito del usuario.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../models/OrdenesModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';
require_once __DIR__ . '/../models/CatalogModel.php';

// Solo acepta peticiones POST para la confirmación de compra
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: carrito.php");
    exit;
}

// Obtiene el carrito de la sesión
$cart = $_SESSION['cart'] ?? [];

// Valida que el carrito no esté vacío
if (empty($cart)) {
    $_SESSION['flash_error'] = 'Tu carrito está vacío.';
    header("Location: carrito.php");
    exit;
}

// Obtiene datos del cliente autenticado
$clienteId = (int)($_SESSION['auth']['UsuarioId'] ?? 0);

// Obtiene y limpia los datos de la compra desde el formulario
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$metodo = trim($_POST['metodo'] ?? '');

// Valida que los campos requeridos estén completos
if ($nombre === '' || $email === '' || $metodo === '') {
    $_SESSION['flash_error'] = 'Completa los datos de compra.';
    header("Location: carrito.php");
    exit;
}

// Prepara los items del carrito para la confirmación
$items = [];
$map = [];

// Obtiene información actualizada de los juegos
try {
    $juegos = CatalogModel::listarJuegos();
    foreach ($juegos as $j) {
        $jid = (int)($j['JuegoId'] ?? 0);
        if ($jid > 0) {
            $map[$jid] = $j;
        }
    }
} catch (Exception $e) {
    // Continúa sin información de juegos si hay error
}

// Construye los items de la orden con información de precio actualizada
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

// Confirma la compra en la base de datos
$res = OrdenesModel::confirmarCompra($clienteId, $items);

// Valida la respuesta de confirmación
if (($res['Status'] ?? '') !== 'OK') {
    $_SESSION['flash_error'] = $res['Message'] ?? 'No se pudo confirmar la compra.';
    header("Location: carrito.php");
    exit;
}

// Almacena los detalles de la orden en la sesión para referencia
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

// Registra cada compra en el sistema de auditoría
foreach ($items as $it) {
    try {
        AuditoriaModel::registrar(
            (int)$clienteId,
            'COMPRAR',
            'Ordenes',
            "Compra de juego: {$it['JuegoId']}"
        );
    } catch (Exception $e) {
        // No bloquea el proceso si falla el registro de auditoría
    }
}

// Limpia el carrito después de completar la compra exitosamente
$_SESSION['cart'] = [];

// Establece mensaje de éxito
$_SESSION['flash_success'] = 'Compra realizada. Revisa tus órdenes.';

// Redirige a la página de órdenes para mostrar el resumen
header("Location: ordenes.php");
exit;



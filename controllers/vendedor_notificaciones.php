<?php
declare(strict_types=1);

/**
 * vendedor_notificaciones.php
 * 
 * Controlador que genera y renderiza las notificaciones del vendedor.
 * Cuenta y resume el estado de las keys publicadas y vendidas.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

// Obtiene los datos de autenticación del vendedor
$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$notificaciones = [];
$error = null;

try {
    // Obtiene todas las keys del vendedor
    $keys = VendedorPerfilModel::misKeys($usuarioId);
    
    // Inicializa contadores para cada estado
    $pendientes = 0;
    $disponibles = 0;
    $vendidas = 0;
    
    // Itera sobre las keys y cuenta por estado
    foreach ($keys as $row) {
        $estado = strtoupper((string)($row['Estado'] ?? ''));
        if ($estado === 'PENDIENTE') {
            $pendientes++;
        } elseif ($estado === 'DISPONIBLE') {
            $disponibles++;
        } elseif ($estado === 'VENDIDA') {
            $vendidas++;
        }
    }
    
    // Construye los mensajes de notificación basados en los conteos
    if ($pendientes > 0) {
        $notificaciones[] = "Tienes {$pendientes} publicación(es) en revisión.";
    }
    if ($disponibles > 0) {
        $notificaciones[] = "Tienes {$disponibles} key(s) disponibles para venta.";
    }
    if ($vendidas > 0) {
        $notificaciones[] = "Se registraron {$vendidas} venta(s). Revisa tus ingresos.";
    }
    
    // Si no hay notificaciones, muestra mensaje por defecto
    if (empty($notificaciones)) {
        $notificaciones[] = "No hay notificaciones nuevas.";
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Carga la vista con las notificaciones
require_once __DIR__ . '/../views/vendedor/notificaciones.php';


<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$notificaciones = [];
$error = null;

try {
    $keys = VendedorPerfilModel::misKeys($usuarioId);
    $pendientes = 0;
    $disponibles = 0;
    $vendidas = 0;
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
    if ($pendientes > 0) {
        $notificaciones[] = "Tienes {$pendientes} publicación(es) en revisión.";
    }
    if ($disponibles > 0) {
        $notificaciones[] = "Tienes {$disponibles} key(s) disponibles para venta.";
    }
    if ($vendidas > 0) {
        $notificaciones[] = "Se registraron {$vendidas} venta(s). Revisa tus ingresos.";
    }
    if (empty($notificaciones)) {
        $notificaciones[] = "No hay notificaciones nuevas.";
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../views/vendedor/notificaciones.php';


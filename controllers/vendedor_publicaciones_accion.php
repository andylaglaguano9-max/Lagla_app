<?php
declare(strict_types=1);

/**
 * vendedor_publicaciones_accion.php
 * 
 * Controlador que procesa acciones sobre las publicaciones pendientes del vendedor.
 * Maneja la actualización y eliminación de keys en estado PENDIENTE.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: vendedor_publicaciones.php");
    exit;
}

// Obtiene los datos del vendedor desde la sesión
$auth = $_SESSION['auth'] ?? [];
$vendedorId = (int)($auth['UsuarioId'] ?? 0);

// Obtiene la acción a realizar y el ID de la key
$accion = $_POST['accion'] ?? '';
$keyId = (int)($_POST['keyId'] ?? 0);

// Valida que los datos sean válidos
if ($vendedorId <= 0 || $keyId <= 0) {
    $_SESSION['flash_error'] = 'Acción no válida.';
    header("Location: vendedor_publicaciones.php");
    exit;
}

try {
    // Procesa la acción solicitada
    if ($accion === 'actualizar') {
        // Obtiene los nuevos datos de la key
        $keyValor = trim($_POST['keyValor'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        
        // Valida que los datos sean válidos
        if ($keyValor === '' || $precio <= 0) {
            $_SESSION['flash_error'] = 'Completa key y precio.';
        } else {
            // Actualiza la key pendiente
            VendedorPerfilModel::actualizarPendiente($keyId, $vendedorId, $keyValor, $precio);
            $_SESSION['flash_success'] = 'Key pendiente actualizada.';
        }
    } elseif ($accion === 'eliminar') {
        // Elimina la key pendiente
        VendedorPerfilModel::eliminarPendiente($keyId, $vendedorId);
        $_SESSION['flash_success'] = 'Key pendiente eliminada.';
    } else {
        $_SESSION['flash_error'] = 'Acción no válida.';
    }
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

// Redirige al listado de publicaciones
header("Location: vendedor_publicaciones.php");
exit;


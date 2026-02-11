<?php
declare(strict_types=1);

/**
 * vendedor_key_publicar.php
 * 
 * Controlador que procesa la publicación de una nueva key de juego por el vendedor.
 * Valida los datos, guarda la key con estado PENDIENTE, registra en auditoría
 * y redirige al panel de publicación.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/VendedorPerfilModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: vendedor_publicar.php");
    exit;
}

// Obtiene los datos del vendedor desde la sesión
$auth = $_SESSION['auth'] ?? [];
$vendedorId = (int)($auth['UsuarioId'] ?? 0);

// Obtiene y limpia los datos de la publicación
$juegoId = (int)($_POST['juegoId'] ?? 0);
$keyValor = trim($_POST['keyValor'] ?? '');
$precio = (float)($_POST['precio'] ?? 0);

// Valida que los campos obligatorios estén completos
if ($vendedorId <= 0 || $juegoId <= 0 || $keyValor === '' || $precio <= 0) {
    $_SESSION['flash_error'] = 'Completa los campos obligatorios para publicar.';
    header("Location: vendedor_publicar.php");
    exit;
}

try {
    // Publica la key con estado PENDIENTE de revisión
    VendedorPerfilModel::publicarKey($juegoId, $vendedorId, $keyValor, $precio);
    
    // Registra el evento de publicación en auditoría
    try {
        AuditoriaModel::registrar($vendedorId, 'PUBLICAR', 'Keys', "Publicó una key para el juego ID {$juegoId}");
    } catch (Exception $e) {
        // No bloquea la publicación si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Key publicada en estado PENDIENTE.';
    header("Location: vendedor_publicar.php");
    exit;
} catch (Exception $e) {
    // Detecta si el error es por key duplicada
    $msg = $e->getMessage();
    if (stripos($msg, 'UNIQUE') !== false || stripos($msg, 'duplicate') !== false) {
        $_SESSION['flash_error'] = 'Esta key ya existe. Usa una clave diferente.';
    } else {
        $_SESSION['flash_error'] = 'No se pudo publicar la key. Verifica los datos e intenta de nuevo.';
    }
    header("Location: vendedor_publicar.php");
    exit;
}

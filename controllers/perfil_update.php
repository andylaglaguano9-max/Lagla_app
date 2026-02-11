<?php
declare(strict_types=1);

/**
 * perfil_update.php
 * 
 * Controlador que procesa la actualización de datos del perfil de usuario.
 * Valida los datos, actualiza la base de datos, sincroniza la sesión
 * y registra el evento en auditoría.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireLogin();

require_once __DIR__ . '/../models/PerfilModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

// Determina el rol del usuario para redireccionarlo al perfil correspondiente
$rol = strtoupper(trim((string)($_SESSION['auth']['Tipo'] ?? '')));
$redirect = ($rol === 'VENDEDOR') ? 'vendedor.php' : 'perfil.php';

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: {$redirect}");
    exit;
}

// Obtiene los datos del usuario desde la sesión
$usuarioId = (int)($_SESSION['auth']['UsuarioId'] ?? 0);

// Obtiene y limpia los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

// Valida que los campos requeridos estén completos
if ($usuarioId <= 0 || $nombre === '' || $email === '') {
    $_SESSION['flash_error'] = 'Completa nombre y correo';
    header("Location: {$redirect}");
    exit;
}

try {
    // Actualiza el perfil en la base de datos
    PerfilModel::actualizarPerfil($usuarioId, $nombre, $email, $telefono);
    
    // Sincroniza los datos actualizados en la sesión
    $_SESSION['auth']['Nombre'] = $nombre;
    $_SESSION['auth']['Email'] = $email;
    $_SESSION['auth']['Telefono'] = $telefono;
    
    // Registra el evento de actualización en auditoría
    try {
        AuditoriaModel::registrar($usuarioId, 'ACTUALIZAR', 'Perfil', 'Actualizó datos de perfil');
    } catch (Exception $e) {
        // No bloquea la actualización si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Perfil actualizado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

// Redirige al perfil del usuario con el mensaje de resultado
header("Location: {$redirect}");
exit;

<?php
declare(strict_types=1);

/**
 * perfil.php
 * 
 * Controlador que renderiza el perfil del usuario autenticado.
 * Obtiene la información del perfil desde la base de datos y la sincroniza
 * con los datos de sesión para mantenerlos actualizados.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireLogin();

require_once __DIR__ . '/../models/PerfilModel.php';

// Obtiene los datos de autenticación del usuario desde la sesión
$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$perfil = null;
$error = null;

// Intenta obtener el perfil completo del usuario desde la base de datos
if ($usuarioId > 0) {
    try {
        $perfil = PerfilModel::obtenerPerfil($usuarioId);
        
        // Si se obtiene el perfil, actualiza los datos de sesión con la información más reciente
        if ($perfil) {
            $auth['Nombre'] = $perfil['Nombre'] ?? $auth['Nombre'] ?? '';
            $auth['Email'] = $perfil['Email'] ?? $auth['Email'] ?? '';
            $auth['Telefono'] = $perfil['Telefono'] ?? $auth['Telefono'] ?? '';
            $auth['Tipo'] = $perfil['Tipo'] ?? $auth['Tipo'] ?? '';
            $_SESSION['auth'] = $auth;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Renderiza la vista del perfil con la información obtenida
require_once __DIR__ . '/../views/perfil/index.php';

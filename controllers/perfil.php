<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireLogin();
require_once __DIR__ . '/../models/PerfilModel.php';

$auth = $_SESSION['auth'] ?? [];
$usuarioId = (int)($auth['UsuarioId'] ?? 0);
$perfil = null;
$error = null;

if ($usuarioId > 0) {
    try {
        $perfil = PerfilModel::obtenerPerfil($usuarioId);
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

require_once __DIR__ . '/../views/perfil/index.php';

<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../models/AuditoriaModel.php';

try {
    $uid = (int)($_SESSION['auth']['UsuarioId'] ?? 0);
    if ($uid > 0) {
        AuditoriaModel::registrar($uid, 'LOGOUT', 'Usuarios', 'Cierre de sesión');
    }
} catch (Exception $e) {
    // No bloquear logout si falla auditoría
}

session_destroy();
header("Location: login.php");
exit;

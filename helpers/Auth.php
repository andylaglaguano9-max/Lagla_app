<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireLogin(): void
{
    if (empty($_SESSION['logged_in'])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole(array $roles = []): void
{
    requireLogin();

    $rol = $_SESSION['auth']['Tipo'] ?? '';

    if (!in_array($rol, $roles, true)) {
        echo "Acceso denegado";
        exit;
    }
}

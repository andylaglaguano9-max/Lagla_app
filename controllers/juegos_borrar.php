<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/admin_juegos.php");
    exit;
}

$juegoId = (int)($_POST['juegoId'] ?? 0);
if ($juegoId <= 0) {
    $_SESSION['flash_error'] = 'Juego inválido';
    header("Location: admin/admin_juegos.php");
    exit;
}

try {
    GameModel::borrarJuego($juegoId);
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'BORRAR',
            'Juegos',
            "Juego eliminado: {$juegoId}"
        );
    } catch (Exception $e) {
        // No bloquear eliminación si falla auditoría
    }
    $_SESSION['flash_success'] = 'Juego eliminado';
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

header("Location: admin/admin_juegos.php");
exit;

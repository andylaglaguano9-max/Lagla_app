<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/VendedorPerfilModel.php';

$auth = $_SESSION['auth'] ?? [];
$juegos = [];
$misKeys = [];
$misKeysRecientes = [];
$error = null;

try {
    $juegos = GameModel::listarJuegos();
    $usuarioId = (int)($auth['UsuarioId'] ?? 0);
    if ($usuarioId > 0) {
        $misKeys = VendedorPerfilModel::misKeys($usuarioId);
        $pendientes = array_values(array_filter($misKeys, static function ($row) {
            return strtoupper((string)($row['Estado'] ?? '')) === 'PENDIENTE';
        }));
        usort($pendientes, static function ($a, $b) {
            return (int)($b['KeyId'] ?? 0) <=> (int)($a['KeyId'] ?? 0);
        });
        $misKeysRecientes = array_slice($pendientes, 0, 5);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../views/vendedor/publicar.php';

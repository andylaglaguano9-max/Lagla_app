<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../../models/VentasModel.php';
require_once __DIR__ . '/../../models/AuditoriaModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyId = (int)($_POST['keyId'] ?? 0);
    if ($keyId > 0) {
        try {
            VentasModel::anularVenta($keyId);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'ANULAR',
                    'Ventas',
                    "Venta anulada: KeyId {$keyId}"
                );
            } catch (Exception $e) {
            }
            $_SESSION['flash_success'] = 'Venta anulada';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
    }
    header("Location: ventas.php");
    exit;
}

$ventas = [];
$error = null;

try {
    $ventas = VentasModel::listarVentas();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/admin/ventas.php';

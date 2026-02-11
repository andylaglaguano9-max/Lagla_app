<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../../models/InventarioModel.php';
require_once __DIR__ . '/../../models/AuditoriaModel.php';

$action = $_POST['action'] ?? '';
$keyId = (int)($_POST['keyId'] ?? 0);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $keyId > 0) {
    try {
        if ($action === 'aprobar') {
            InventarioModel::aprobarKey($keyId);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'APROBAR',
                    'Keys',
                    "Key aprobada: {$keyId}"
                );
            } catch (Exception $e) {
            }
            $_SESSION['flash_success'] = 'Key aprobada';
        } elseif ($action === 'rechazar') {
            InventarioModel::rechazarKey($keyId);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'RECHAZAR',
                    'Keys',
                    "Key rechazada: {$keyId}"
                );
            } catch (Exception $e) {
            }
            $_SESSION['flash_success'] = 'Key rechazada';
        } elseif ($action === 'vender') {
            InventarioModel::venderKey($keyId);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'VENDER',
                    'Ventas',
                    "Key vendida manualmente: {$keyId}"
                );
            } catch (Exception $e) {
            }
            $_SESSION['flash_success'] = 'Key marcada como vendida';
        } elseif ($action === 'desactivar') {
            InventarioModel::desactivarKey($keyId);
            try {
                AuditoriaModel::registrar(
                    (int)($_SESSION['auth']['UsuarioId'] ?? 0),
                    'DESACTIVAR',
                    'Keys',
                    "Key desactivada: {$keyId}"
                );
            } catch (Exception $e) {
            }
            $_SESSION['flash_success'] = 'Key desactivada';
        }
    } catch (Exception $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header("Location: inventario.php");
    exit;
}

$inventario = [];
$pendientes = [];
$error = null;

try {
    $inventario = InventarioModel::listarInventario();
    $pendientes = InventarioModel::listarPendientes();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/admin/inventario.php';

<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);

require_once __DIR__ . '/../../models/AuditoriaModel.php';

$logs = [];
$error = null;
try {
    $logs = AuditoriaModel::listar();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/admin/auditoria.php';

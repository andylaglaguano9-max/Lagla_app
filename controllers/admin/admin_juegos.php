<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../../models/GameModel.php';

$juegos = [];
$error = null;

try {
    $juegos = GameModel::listarJuegos();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/admin/juegos_list.php';

<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../models/OrdenesModel.php';

$juegoId = (int)($_POST['JuegoId'] ?? $_GET['JuegoId'] ?? 0);

if ($juegoId <= 0) {
    $error = 'Juego inválido';
    require __DIR__ . '/../views/catalog/comprar.php';
    exit;
}

if (empty($_SESSION['auth']['UsuarioId'])) {
    $error = 'Debes iniciar sesión para comprar';
    require __DIR__ . '/../views/catalog/comprar.php';
    exit;
}

$clienteId = (int)$_SESSION['auth']['UsuarioId'];

$resultado = OrdenesModel::comprarJuego($juegoId, $clienteId);

$status  = $resultado['Status'] ?? 'ERROR';
$message = $resultado['Message'] ?? 'Error desconocido';

require __DIR__ . '/../views/catalog/comprar.php';

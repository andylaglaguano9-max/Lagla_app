<?php
session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../models/OrdenesModel.php';

$clienteId = intval($_SESSION['auth']['UsuarioId']);


$resultDetalle = OrdenesModel::listarMisOrdenesDetalle($clienteId);
$ordenes = $resultDetalle['data'] ?? [];
$message = $resultDetalle['message'] ?? null;

$resultAntiguas = OrdenesModel::listarMisOrdenes($clienteId);
$ordenesAntiguas = $resultAntiguas['data'] ?? [];

require_once __DIR__ . '/../views/ordenes/index.php';

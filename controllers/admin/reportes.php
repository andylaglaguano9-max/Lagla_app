<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../helpers/Auth.php';
requireRole(['ADMIN']);
require_once __DIR__ . '/../../models/ReportesModel.php';

$error = null;
$ventas = [];
$ingresos = ['TotalVentas' => 0, 'TotalIngresos' => 0];
$masVendidos = [];

$inicio = $_GET['inicio'] ?? date('Y-m-01');
$fin = $_GET['fin'] ?? date('Y-m-d');

try {
    $ventas = ReportesModel::ventasPorFecha($inicio, $fin);
    $ingresos = ReportesModel::ingresosTotales();
    $masVendidos = ReportesModel::juegosMasVendidos();
} catch (Exception $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../../views/reportes/index.php';

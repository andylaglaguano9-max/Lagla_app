<?php
declare(strict_types=1);

/**
 * ordenes.php
 * 
 * Controlador que renderiza el historial de órdenes del cliente.
 * Obtiene tanto órdenes recientes como históricas del usuario autenticado.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['CLIENTE']);

require_once __DIR__ . '/../models/OrdenesModel.php';

// Obtiene el ID del cliente desde la sesión
$clienteId = intval($_SESSION['auth']['UsuarioId']);

// Obtiene las órdenes recientes con sus detalles
$resultDetalle = OrdenesModel::listarMisOrdenesDetalle($clienteId);
$ordenes = $resultDetalle['data'] ?? [];
$message = $resultDetalle['message'] ?? null;

// Obtiene el historial completo de órdenes antiguas del cliente
$resultAntiguas = OrdenesModel::listarMisOrdenes($clienteId);
$ordenesAntiguas = $resultAntiguas['data'] ?? [];

// Renderiza la vista de órdenes con todos los datos obtenidos
require_once __DIR__ . '/../views/ordenes/index.php';

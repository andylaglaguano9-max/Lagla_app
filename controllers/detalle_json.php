<?php
declare(strict_types=1);
session_start();

header('Content-Type: application/json');

if (empty($_SESSION['logged_in'])) {
    echo json_encode(['Status' => 'ERROR', 'Message' => 'No autenticado']);
    exit;
}

require_once __DIR__ . '/../models/CatalogModel.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['Status' => 'ERROR', 'Message' => 'Juego no válido']);
    exit;
}

$juegoId = (int)$_GET['id'];
$juego = CatalogModel::obtenerDetalleJuego($juegoId);

// Fallback: si falta genero/descripcion, tomar del listado del SP
if ($juego) {
    // Normalizar posibles nombres de columnas
    if (empty($juego['Genero'])) {
        $juego['Genero'] = $juego['Categoria'] ?? $juego['Tipo'] ?? $juego['NombreGenero'] ?? null;
    }
    if (empty($juego['Descripcion'])) {
        $juego['Descripcion'] = $juego['Detalle'] ?? $juego['Sinopsis'] ?? null;
    }

    $needsGenero = empty($juego['Genero']);
    $needsDesc = empty($juego['Descripcion']);
    if ($needsGenero || $needsDesc) {
        $lista = CatalogModel::listarJuegos();
        foreach ($lista as $item) {
            $id = $item['JuegoId'] ?? $item['IdJuego'] ?? $item['id_juego'] ?? $item['id'] ?? null;
            if ((int)$id === $juegoId) {
                if ($needsGenero) {
                    $juego['Genero'] = $item['Genero']
                        ?? $item['Categoria']
                        ?? $item['Tipo']
                        ?? $item['NombreGenero']
                        ?? $juego['Genero'];
                }
                if ($needsDesc) {
                    $juego['Descripcion'] = $item['Descripcion']
                        ?? $item['Detalle']
                        ?? $item['Sinopsis']
                        ?? $juego['Descripcion'];
                }
                if (empty($juego['Plataforma']) && !empty($item['Plataforma'])) {
                    $juego['Plataforma'] = $item['Plataforma'];
                }
                if (empty($juego['Precio']) && isset($item['Precio'])) {
                    $juego['Precio'] = $item['Precio'];
                }
                break;
            }
        }
    }
}

if (!$juego) {
    echo json_encode(['Status' => 'ERROR', 'Message' => 'Juego no encontrado']);
    exit;
}

echo json_encode(['Status' => 'OK', 'Data' => $juego]);

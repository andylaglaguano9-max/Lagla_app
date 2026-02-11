<?php
declare(strict_types=1);

/**
 * detalle_json.php
 * 
 * Controlador de API que retorna los detalles de un juego en formato JSON.
 * Valida la autenticación, obtiene la información del juego y realiza búsquedas
 * fallback para campos faltantes (género, descripción, etc).
 */

session_start();

// Define el formato de respuesta como JSON
header('Content-Type: application/json');

// Valida que el usuario esté autenticado antes de acceder a la información
if (empty($_SESSION['logged_in'])) {
    echo json_encode(['Status' => 'ERROR', 'Message' => 'No autenticado']);
    exit;
}

require_once __DIR__ . '/../models/CatalogModel.php';

// Valida que se haya proporcionado un ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['Status' => 'ERROR', 'Message' => 'Juego no válido']);
    exit;
}

// Obtiene y convierte el ID del juego a entero
$juegoId = (int)$_GET['id'];

// Obtiene los detalles del juego desde el modelo
$juego = CatalogModel::obtenerDetalleJuego($juegoId);

// Si el juego existe, intenta completar información faltante desde el listado
if ($juego) {
    // Normaliza posibles variaciones en los nombres de columnas para género
    if (empty($juego['Genero'])) {
        $juego['Genero'] = $juego['Categoria'] ?? $juego['Tipo'] ?? $juego['NombreGenero'] ?? null;
    }
    
    // Normaliza posibles variaciones en los nombres de columnas para descripción
    if (empty($juego['Descripcion'])) {
        $juego['Descripcion'] = $juego['Detalle'] ?? $juego['Sinopsis'] ?? null;
    }

    // Verifica si faltan campos críticos
    $needsGenero = empty($juego['Genero']);
    $needsDesc = empty($juego['Descripcion']);
    
    // Si faltan campos, intenta completarlos desde el listado general de juegos
    if ($needsGenero || $needsDesc) {
        $lista = CatalogModel::listarJuegos();
        foreach ($lista as $item) {
            // Busca el juego en el listado con diferentes nombres de columna posibles
            $id = $item['JuegoId'] ?? $item['IdJuego'] ?? $item['id_juego'] ?? $item['id'] ?? null;
            if ((int)$id === $juegoId) {
                // Completa el género si falta
                if ($needsGenero) {
                    $juego['Genero'] = $item['Genero']
                        ?? $item['Categoria']
                        ?? $item['Tipo']
                        ?? $item['NombreGenero']
                        ?? $juego['Genero'];
                }
                
                // Completa la descripción si falta
                if ($needsDesc) {
                    $juego['Descripcion'] = $item['Descripcion']
                        ?? $item['Detalle']
                        ?? $item['Sinopsis']
                        ?? $juego['Descripcion'];
                }
                
                // Completa otros campos si faltan
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

// Valida que el juego exista
if (!$juego) {
    echo json_encode(['Status' => 'ERROR', 'Message' => 'Juego no encontrado']);
    exit;
}

// Retorna los detalles del juego en formato JSON
echo json_encode(['Status' => 'OK', 'Data' => $juego]);

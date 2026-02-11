<?php
declare(strict_types=1);

/**
 * vendedor_publicar.php
 * 
 * Controlador que renderiza el panel de publicación de keys del vendedor.
 * Obtiene el listado de juegos, las keys del vendedor y filtra las pendientes de revisión.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['VENDEDOR']);

require_once __DIR__ . '/../models/GameModel.php';
require_once __DIR__ . '/../models/VendedorPerfilModel.php';

// Obtiene los datos de autenticación del vendedor
$auth = $_SESSION['auth'] ?? [];
$juegos = [];
$misKeys = [];
$misKeysRecientes = [];
$error = null;

try {
    // Obtiene el catálogo completo de juegos disponibles
    $juegos = GameModel::listarJuegos();
    
    // Obtiene las keys publicadas por el vendedor
    $usuarioId = (int)($auth['UsuarioId'] ?? 0);
    if ($usuarioId > 0) {
        $misKeys = VendedorPerfilModel::misKeys($usuarioId);
        
        // Filtra solo las keys en estado PENDIENTE
        $pendientes = array_values(array_filter($misKeys, static function ($row) {
            return strtoupper((string)($row['Estado'] ?? '')) === 'PENDIENTE';
        }));
        
        // Ordena las keys por ID descendente (más recientes primero)
        usort($pendientes, static function ($a, $b) {
            return (int)($b['KeyId'] ?? 0) <=> (int)($a['KeyId'] ?? 0);
        });
        
        // Obtiene las últimas 5 keys pendientes
        $misKeysRecientes = array_slice($pendientes, 0, 5);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Carga la vista del panel de publicación
require_once __DIR__ . '/../views/vendedor/publicar.php';

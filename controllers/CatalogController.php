<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/CatalogModel.php';

/**
 * CatalogController
 * 
 * Gestiona la visualización del catálogo de juegos en la aplicación.
 * Recupera la lista de juegos disponibles y maneja los mensajes de estado
 * relacionados con la disponibilidad de la base de datos.
 */
class CatalogController
{
    /**
     * index()
     * 
     * Renderiza el catálogo de juegos disponibles.
     * Verifica la autenticación del usuario, obtiene la lista de juegos del modelo,
     * recupera mensajes de error de la sesión si los hay, y los limpia después de leerlos.
     * 
     * @return void
     */
    public function index(): void
    {
        // Valida que el usuario tenga una sesión activa
        if (empty($_SESSION['logged_in'])) {
            header("Location: /gbb-php-mvc/login");
            exit;
        }
        
        // Obtiene la lista completa de juegos disponibles en el catálogo
        $juegos = CatalogModel::listarJuegos();
        
        // Recupera mensajes de estado sobre disponibilidad de la base de datos
        $error = $_SESSION['db_offline_message'] ?? null;
        
        // Limpia el mensaje de sesión después de leerlo para evitar visualizaciones repetidas
        if (isset($_SESSION['db_offline_message'])) unset($_SESSION['db_offline_message']);
        
        require __DIR__ . '/../views/catalog/index.php';
    }
}

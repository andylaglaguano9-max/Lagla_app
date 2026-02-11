<?php
declare(strict_types=1);

/**
 * HomeController
 * 
 * Controlador de la página de inicio de la aplicación.
 * Verifica la autenticación del usuario antes de mostrar el contenido del dashboard.
 */
class HomeController
{
    /**
     * index()
     * 
     * Renderiza la página principal de la aplicación.
     * Valida que el usuario tenga una sesión activa. Si no está autenticado,
     * redirige al formulario de login. En caso contrario, carga los datos
     * de autenticación desde la sesión y presenta la vista de inicio.
     * 
     * @return void
     */
    public function index(): void
    {
        // Verifica si el usuario está autenticado
        if (empty($_SESSION['logged_in'])) {
            header("Location: /gbb-php-mvc/login");
            exit;
        }

        // Obtiene los datos de autenticación del usuario desde la sesión
        $auth = $_SESSION['auth'] ?? [];
        require __DIR__ . '/../views/home/index.php';
    }
}

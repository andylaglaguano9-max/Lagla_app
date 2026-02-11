<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/AuthModel.php';

/**
 * AuthController
 * 
 * Gestiona la autenticación de usuarios en la aplicación.
 * Proporciona funcionalidades de login, logout y visualización del formulario de acceso.
 */
class AuthController
{
    /**
     * show()
     * 
     * Renderiza la vista del formulario de login.
     * Inicializa la variable de error como null para indicar que no hay errores previos.
     * 
     * @return void
     */
    public function show(): void
    {
        $error = null;
        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * login()
     * 
     * Procesa la autenticación del usuario mediante credenciales.
     * Obtiene las credenciales del formulario, valida contra la base de datos,
     * establece las sesiones necesarias y redirige a la página de inicio.
     * Si ocurre un error, captura la excepción y vuelve a mostrar el formulario.
     * 
     * @return void
     */
    public function login(): void
    {
        try {
            // Obtiene y limpia las credenciales del formulario
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Valida que ambos campos hayan sido completados
            if ($email === '' || $password === '') {
                throw new Exception('Ingresa tu correo y tu contraseña.');
            }

            // Intenta autenticar al usuario con las credenciales proporcionadas
            $user = AuthModel::login($email, $password);

            if (!$user) {
                throw new Exception('Credenciales inválidas. Verifica e intenta de nuevo.');
            }

            // Establece las variables de sesión para mantener el estado de autenticación
            $_SESSION['logged_in'] = true;
            $_SESSION['auth'] = [
                'UsuarioId' => $user['UsuarioId'],
                'Nombre' => $user['Nombre'],
                'Email' => $user['Email'],
                'Tipo' => $user['Tipo']
            ];

            header("Location: /gbb-php-mvc/");
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    /**
     * logout()
     * 
     * Cierra la sesión del usuario actual.
     * Destruye todas las variables de sesión y redirige a la página de login.
     * 
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        header("Location: /gbb-php-mvc/login");
        exit;
    }
}

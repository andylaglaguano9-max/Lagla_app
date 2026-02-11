<?php
/**
 * do_login.php
 * 
 * Controlador de procesamiento de login.
 * Valida las credenciales del usuario, establece la sesión correspondiente
 * y registra el evento de acceso en la auditoría del sistema.
 */

session_start();
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

// Obtiene y limpia los datos del formulario de login
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Valida que ambos campos hayan sido completados
if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Ingresa tu correo y tu contraseña.';
    header("Location: login.php");
    exit;
}

// Intenta autenticar al usuario con las credenciales proporcionadas
$user = AuthModel::login($email, $password);

if ($user) {
    // Establece las variables de sesión para mantener el estado de autenticación
    $_SESSION['logged_in'] = true;

    // Almacena la información básica del usuario autenticado
    $_SESSION['auth'] = [
        'UsuarioId' => $user['UsuarioId'],
        'Nombre' => $user['Nombre'],
        'Email' => $user['Email'],
        'Tipo' => $user['Tipo']
    ];

    // Registra el evento de login en el sistema de auditoría
    try {
        AuditoriaModel::registrar(
            (int)$user['UsuarioId'],
            'LOGIN',
            'Usuarios',
            'Inicio de sesión'
        );
    } catch (Exception $e) {
        // No bloquea el login si falla el registro de auditoría
    }

    // Redirige al usuario a la página de inicio después de autenticarse exitosamente
    header("Location: home.php");
    exit;
}

// Si las credenciales son inválidas, muestra mensaje de error y redirige al login
$_SESSION['login_error'] = 'Credenciales inválidas. Verifica e intenta de nuevo.';
header("Location: login.php");
exit;

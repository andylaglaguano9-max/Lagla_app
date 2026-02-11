<?php
session_start();
require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['login_error'] = 'Ingresa tu correo y tu contraseña.';
    header("Location: login.php");
    exit;
}

$user = AuthModel::login($email, $password);

if ($user) {
    $_SESSION['logged_in'] = true;

    $_SESSION['auth'] = [
        'UsuarioId' => $user['UsuarioId'],
        'Nombre' => $user['Nombre'],
        'Email' => $user['Email'],
        'Tipo' => $user['Tipo']
    ];

    try {
        AuditoriaModel::registrar(
            (int)$user['UsuarioId'],
            'LOGIN',
            'Usuarios',
            'Inicio de sesión'
        );
    } catch (Exception $e) {
        // No bloquear login si falla auditoría
    }

    header("Location: home.php");
    exit;
}

$_SESSION['login_error'] = 'Credenciales inválidas. Verifica e intenta de nuevo.';
header("Location: login.php");
exit;

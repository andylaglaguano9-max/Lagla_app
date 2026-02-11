<?php
declare(strict_types=1);

/**
 * usuarios_guardar.php
 * 
 * Controlador que procesa la creación de un nuevo usuario.
 * Valida los datos, encripta la contraseña, guarda el usuario en base de datos,
 * registra el evento en auditoría y redirige al listado de usuarios.
 */

session_start();
require_once __DIR__ . '/../helpers/Auth.php';
requireRole(['ADMIN']);

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AuditoriaModel.php';

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin/usuarios.php");
    exit;
}

// Obtiene y limpia los datos del formulario
$tipo = trim($_POST['tipo'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$estado = isset($_POST['estado']) ? 1 : 0;

// Valida que los campos obligatorios estén completos
if ($tipo === '' || $nombre === '' || $email === '' || $password === '') {
    $_SESSION['flash_error'] = 'Completa todos los campos';
    header("Location: usuarios_crear.php");
    exit;
}

// Encripta la contraseña usando el algoritmo PASSWORD_DEFAULT
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Crea el nuevo usuario en la base de datos
    UserModel::crearUsuario($tipo, $nombre, $email, $hash, $estado);
    
    // Registra el evento de creación en auditoría
    try {
        AuditoriaModel::registrar(
            (int)($_SESSION['auth']['UsuarioId'] ?? 0),
            'CREAR',
            'Usuarios',
            "Usuario creado: {$nombre} ({$email})"
        );
    } catch (Exception $e) {
        // No bloquea la creación si falla auditoría
    }
    
    $_SESSION['flash_success'] = 'Usuario creado';
    header("Location: admin/usuarios.php");
    exit;
} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
    header("Location: usuarios_crear.php");
    exit;
}

<?php
declare(strict_types=1);
session_start();
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

require_once __DIR__ . '/../views/auth/login.php';

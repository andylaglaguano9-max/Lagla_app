<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/AuthModel.php';

class AuthController
{
    public function show(): void
    {
        $error = null;
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        try {
            $info = AuthModel::loginInfo();

            $_SESSION['logged_in'] = true;
            $_SESSION['auth'] = $info; // raw output SP

            header("Location: /gbb-php-mvc/");
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
            require __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout(): void
    {
        session_destroy();
        header("Location: /gbb-php-mvc/login");
        exit;
    }
}

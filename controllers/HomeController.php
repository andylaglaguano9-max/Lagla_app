<?php
declare(strict_types=1);

class HomeController
{
    public function index(): void
    {
        if (empty($_SESSION['logged_in'])) {
            header("Location: /gbb-php-mvc/login");
            exit;
        }

        $auth = $_SESSION['auth'] ?? [];
        require __DIR__ . '/../views/home/index.php';
    }
}

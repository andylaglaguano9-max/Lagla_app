<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/CatalogModel.php';

class CatalogController
{
    public function index(): void
    {
        if (empty($_SESSION['logged_in'])) {
            header("Location: /gbb-php-mvc/login");
            exit;
        }
        $juegos = CatalogModel::listarJuegos();
        $error = $_SESSION['db_offline_message'] ?? null;
        // clear session message after reading
        if (isset($_SESSION['db_offline_message'])) unset($_SESSION['db_offline_message']);
        require __DIR__ . '/../views/catalog/index.php';
    }
}

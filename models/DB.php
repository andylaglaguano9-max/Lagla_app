<?php
declare(strict_types=1);

class DB
{
    private static ?PDO $cn = null;

    public static function conn(): PDO
    {
        if (self::$cn === null) {
            // SQL Server HOST (Esteban)
            $dsn = "sqlsrv:Server=10.26.208.203;Database=GBB_Host";

            self::$cn = new PDO(
                $dsn,
                "app_gbb",
                "AppGbb@123",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        return self::$cn;
    }
}

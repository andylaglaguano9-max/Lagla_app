<?php
declare(strict_types=1);

/**
 * DB
 * 
 * Clase singleton que gestiona la conexión a la base de datos SQL Server.
 * Proporciona un único punto de acceso a la conexión PDO con configuraciones
 * de manejo de errores y modo de obtención de datos.
 */
class DB
{
    private static ?PDO $cn = null;

    /**
     * conn()
     * 
     * Retorna la instancia única de la conexión PDO a SQL Server.
     * Si no existe una conexión activa, crea una nueva conexión al servidor
     * SQL Server con credenciales de la aplicación.
     * 
     * Servidor: 10.26.208.203 (Esteban)
     * Base de datos: GBB_Host
     * Usuario: app_gbb
     * 
     * @return PDO La conexión a la base de datos.
     */
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

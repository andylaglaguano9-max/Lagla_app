<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

/**
 * ConfiguracionModel
 * 
 * System administration interface for global application settings and catalog configuration.
 * Manages both key-value system parameters and the game platform master data that organizes
 * games into categories (PC, Xbox, PlayStation, etc) and theme/styling preferences.
 * 
 * Operations split between:
 * - ParametrosSistema table: Global configuration key-value pairs
 * - Plataformas table: Game platform descriptions with active/inactive status
 * - Theme management: UI theme selection and activation
 */
class ConfiguracionModel {

    /**
     * listarParametros()
     * 
     * Retrieves all system configuration parameters as key-value pairs with descriptions.
     * Used by admin panel to display configurable application settings (e.g., max price,
     * minimum order amount, feature flags, API endpoints, etc).
     * 
     * Query: SELECT FROM ParametrosSistema table
     * 
     * @return array Array of configuration rows:
     *         [
     *             ['Parametro' => string, 'Valor' => string, 'Descripcion' => string],
     *             ...
     *         ]
     */
    public static function listarParametros() {
        $db = DB::conn();
        $stmt = $db->prepare("SELECT Parametro, Valor, Descripcion FROM [10.26.208.149].GBB_Remoto.dbo.ParametrosSistema");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * actualizarParametro()
     * 
     * Updates a single system configuration parameter by key name. Changes take effect
     * after application cache reload or session refresh depending on caching layer.
     * 
     * @param string $param The Parametro key to update
     * @param string $valor New Valor value to set
     * @return bool True if UPDATE succeeded, false on failure or no-rows-affected
     */
    public static function actualizarParametro(string $param, string $valor): bool {
        $db = DB::conn();
        $stmt = $db->prepare("UPDATE [10.26.208.149].GBB_Remoto.dbo.ParametrosSistema SET Valor = :valor WHERE Parametro = :param");
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':param', $param);
        return $stmt->execute();
    }

    /**
     * listarPlataformas()
     * 
     * Retrieves all game platforms (console/distribution channels) that organize game catalog.
     * Platforms appear in game detail pages and filtering UI. Estado indicates active (1) or archived (0).
     * 
     * Query: SELECT FROM Plataformas table
     * 
     * @return array Array of platform definitions:
     *         [
     *             ['PlataformaId' => int, 'Nombre' => string, 'Estado' => int],
     *             ...
     *         ]
     */
    public static function listarPlataformas() {
        $db = DB::conn();
        $stmt = $db->prepare("SELECT PlataformaId, Nombre, Estado FROM [10.26.208.149].GBB_Remoto.dbo.Plataformas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * crearPlataforma()
     * 
     * Inserts a new game platform into the Plataformas master table. Platforms must be created
     * before games can be associated with them in the catalog.
     * 
     * @param string $nombre Platform name (e.g., "PlayStation 5", "Nintendo Switch", "Steam PC")
     * @param int $estado Active status (1 = active, 0 = archived/inactive)
     * @return bool True if INSERT succeeded, false on failure
     */
    public static function crearPlataforma(string $nombre, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("INSERT INTO [10.26.208.149].GBB_Remoto.dbo.Plataformas (Nombre, Estado) VALUES (:nombre, :estado)");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * actualizarPlataforma()
     * 
     * Updates an existing platform's name and status. Useful for renaming platforms or
     * deactivating them (Estado = 0) to archive them without deleting historical game records.
     * 
     * @param int $id PlataformaId to update
     * @param string $nombre New platform name
     * @param int $estado New active status (1 = active, 0 = inactive)
     * @return bool True if UPDATE succeeded, false on failure
     */
    public static function actualizarPlataforma(int $id, string $nombre, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("UPDATE [10.26.208.149].GBB_Remoto.dbo.Plataformas SET Nombre = :nombre, Estado = :estado WHERE PlataformaId = :id");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * listarTemas()
     * 
     * Retrieves all available UI themes. Each theme contains CSS configuration and branding
     * options. Themes are selected for activation via activarTema() and stored in admin preferences.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Temas_Listar
     * 
     * @return array Array of theme definitions with structure including:
     *         [
     *             ['TemaId' => int, 'Nombre' => string, 'Activo' => int, ...],
     *             ...
     *         ]
     */
    public static function listarTemas() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Temas_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * activarTema()
     * 
     * Activates a specific UI theme system-wide. Deactivates any previously active theme
     * (procedure implementation ensures only one active theme at a time). Changes affect
     * all subsequent user sessions.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Tema_Activar
     * 
     * @param int $temaId TemaId to activate
     * @return bool True if activation succeeded, false on failure
     */
    public static function activarTema(int $temaId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Tema_Activar @TemaId = :id");
        $stmt->bindValue(':id', $temaId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

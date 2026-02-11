<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

/**
 * TemaModel
 * 
 * UI theme selection utility for retrieving the currently active theme across the application.
 * Themes define CSS, color schemes, and branding elements. This model provides a cached/filtered
 * view of available themes to identify which one is currently active.
 */
class TemaModel {

    /**
     * temaActivo()
     * 
     * Retrieves the currently active theme from the system. Iterates through all available themes
     * returned by sp_Temas_Listar and returns the first theme where Activo flag = 1.
     * 
     * If no active theme is found, returns null (edge case indicating configuration error).
     * Application should provide a default fallback theme in this scenario.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Temas_Listar
     * 
     * @return array|null Single active theme row with structure:
     *         ['TemaId' => int, 'Nombre' => string, 'Activo' => int (1), ...]
     *         or null if no theme has Activo = 1
     */
    public static function temaActivo(): ?array {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Temas_Listar");
        $stmt->execute();
        $temas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($temas as $t) {
            if ((int)($t['Activo'] ?? 0) === 1) {
                return $t;
            }
        }
        return null;
    }
}

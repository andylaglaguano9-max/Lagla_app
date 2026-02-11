<?php
require_once __DIR__ . '/DB.php';

class TemaModel {

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

<?php
require_once __DIR__ . '/DB.php';

class ConfiguracionModel {

    public static function listarParametros() {
        $db = DB::conn();
        $stmt = $db->prepare("SELECT Parametro, Valor, Descripcion FROM [10.26.208.149].GBB_Remoto.dbo.ParametrosSistema");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizarParametro(string $param, string $valor): bool {
        $db = DB::conn();
        $stmt = $db->prepare("UPDATE [10.26.208.149].GBB_Remoto.dbo.ParametrosSistema SET Valor = :valor WHERE Parametro = :param");
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':param', $param);
        return $stmt->execute();
    }

    public static function listarPlataformas() {
        $db = DB::conn();
        $stmt = $db->prepare("SELECT PlataformaId, Nombre, Estado FROM [10.26.208.149].GBB_Remoto.dbo.Plataformas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crearPlataforma(string $nombre, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("INSERT INTO [10.26.208.149].GBB_Remoto.dbo.Plataformas (Nombre, Estado) VALUES (:nombre, :estado)");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function actualizarPlataforma(int $id, string $nombre, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("UPDATE [10.26.208.149].GBB_Remoto.dbo.Plataformas SET Nombre = :nombre, Estado = :estado WHERE PlataformaId = :id");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function listarTemas() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Temas_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function activarTema(int $temaId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Tema_Activar @TemaId = :id");
        $stmt->bindValue(':id', $temaId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

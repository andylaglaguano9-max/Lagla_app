<?php
require_once __DIR__ . '/DB.php';

class GameModel {

    public static function listarJuegos() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerJuego(int $juegoId) {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Obtener @JuegoId = :id");
        $stmt->bindValue(':id', $juegoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function crearJuego(int $plataformaId, string $nombre, string $descripcion, float $precio, string $genero, string $imagenUrl): bool {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Crear
                @PlataformaId = :plataforma,
                @Nombre = :nombre,
                @Descripcion = :descripcion,
                @Precio = :precio,
                @Genero = :genero,
                @ImagenUrl = :imagen
        ");
        $stmt->bindValue(':plataforma', $plataformaId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':precio', $precio);
        $stmt->bindValue(':genero', $genero);
        $stmt->bindValue(':imagen', $imagenUrl);
        return $stmt->execute();
    }

    public static function editarJuego(int $juegoId, int $plataformaId, string $nombre, string $descripcion, float $precio, string $genero, string $imagenUrl, int $estado): bool {
        $db = DB::conn();
        $stmt = $db->prepare("
            EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Editar
                @JuegoId = :id,
                @PlataformaId = :plataforma,
                @Nombre = :nombre,
                @Descripcion = :descripcion,
                @Precio = :precio,
                @Genero = :genero,
                @ImagenUrl = :imagen,
                @Estado = :estado
        ");
        $stmt->bindValue(':id', $juegoId, PDO::PARAM_INT);
        $stmt->bindValue(':plataforma', $plataformaId, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':precio', $precio);
        $stmt->bindValue(':genero', $genero);
        $stmt->bindValue(':imagen', $imagenUrl);
        $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function borrarJuego(int $juegoId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Borrar @JuegoId = :id");
        $stmt->bindValue(':id', $juegoId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

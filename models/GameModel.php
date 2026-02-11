<?php
/**
 * GameModel
 * 
 * Modelo que gestiona operaciones CRUD de juegos en la plataforma.
 * Todas las operaciones se realizan mediante procedimientos almacenados
 * en el servidor remoto GBB_Remoto.
 */

require_once __DIR__ . '/DB.php';

class GameModel {

    /**
     * listarJuegos()
     * 
     * Obtiene el listado completo de juegos disponibles en el sistema.
     * Invoca el procedimiento almacenado sp_Juegos_Listar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Listar
     * 
     * @return array Lista de todos los juegos con sus datos (ID, nombre, precio, etc)
     */
    public static function listarJuegos() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * obtenerJuego()
     * 
     * Obtiene los detalles de un juego específico por su ID.
     * Invoca el procedimiento almacenado sp_Juegos_Obtener.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Obtener
     * 
     * @param int $juegoId Identificador único del juego
     * @return array|null Datos completos del juego o null si no existe
     */
    public static function obtenerJuego(int $juegoId) {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Obtener @JuegoId = :id");
        $stmt->bindValue(':id', $juegoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * crearJuego()
     * 
     * Crea un nuevo juego en la base de datos.
     * Invoca el procedimiento almacenado sp_Juegos_Crear con los datos del juego.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Crear
     * 
     * @param int $plataformaId Identificador de la plataforma (PS5, Xbox, PC, etc)
     * @param string $nombre Nombre del juego
     * @param string $descripcion Descripción detallada del juego
     * @param float $precio Precio de venta del juego
     * @param string $genero Género del juego (Acción, RPG, Aventura, etc)
     * @param string $imagenUrl URL de la imagen de portada del juego
     * @return bool true si la creación fue exitosa, false en caso de error
     */
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

    /**
     * editarJuego()
     * 
     * Actualiza los datos de un juego existente.
     * Invoca el procedimiento almacenado sp_Juegos_Editar con los nuevos datos.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Editar
     * 
     * @param int $juegoId Identificador del juego a actualizar
     * @param int $plataformaId Identificador de la plataforma
     * @param string $nombre Nuevo nombre del juego
     * @param string $descripcion Nueva descripción
     * @param float $precio Nuevo precio
     * @param string $genero Nuevo género
     * @param string $imagenUrl Nueva URL de imagen
     * @param int $estado Estado del juego (1 = activo, 0 = inactivo)
     * @return bool true si la actualización fue exitosa, false en caso de error
     */
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

    /**
     * borrarJuego()
     * 
     * Elimina un juego de la base de datos.
     * Invoca el procedimiento almacenado sp_Juegos_Borrar.
     * 
     * Procedimiento: [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Borrar
     * 
     * @param int $juegoId Identificador del juego a eliminar
     * @return bool true si la eliminación fue exitosa, false en caso de error
     */
    public static function borrarJuego(int $juegoId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Juegos_Borrar @JuegoId = :id");
        $stmt->bindValue(':id', $juegoId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

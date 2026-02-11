<?php
require_once __DIR__ . '/DB.php';

class ReportesModel {

    public static function ventasPorFecha(string $inicio, string $fin) {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Ventas_Por_Fecha @FechaInicio = :ini, @FechaFin = :fin");
        $stmt->bindValue(':ini', $inicio);
        $stmt->bindValue(':fin', $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ingresosTotales() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Ingresos");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['TotalVentas' => 0, 'TotalIngresos' => 0];
    }

    public static function juegosMasVendidos() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Juegos_Mas_Vendidos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

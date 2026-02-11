<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

/**
 * ReportesModel
 * 
 * Analytics and reporting interface for business intelligence. Provides aggregated data
 * across sales, revenue, and product performance metrics via remote stored procedures
 * on the GBB_Remoto database.
 * 
 * All procedures execute against the remote server [10.26.208.149].GBB_Remoto.dbo
 * to provide system-wide visibility across federated marketplace operations.
 */
class ReportesModel {

    /**
     * ventasPorFecha()
     * 
     * Retrieves sales transaction data for a specified date range. Used for daily/weekly/monthly
     * performance analysis and reconciliation reports. Returns one row per sale transaction
     * with associated metadata (amount, game, customer, timestamp, etc).
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Ventas_Por_Fecha
     * 
     * @param string $inicio Start date in YYYY-MM-DD format (inclusive)
     * @param string $fin End date in YYYY-MM-DD format (inclusive)
     * @return array Array of transaction rows with structure defined by stored procedure result set
     *               (typically includes: VentaId, Fecha, Monto, NombreJuego, ClienteEmail, etc)
     */
    public static function ventasPorFecha(string $inicio, string $fin) {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Ventas_Por_Fecha @FechaInicio = :ini, @FechaFin = :fin");
        $stmt->bindValue(':ini', $inicio);
        $stmt->bindValue(':fin', $fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ingresosTotales()
     * 
     * Calculates aggregate revenue metrics across entire system: total transaction count
     * and total revenue generated from all game sales. Single-row result set used for
     * dashboard summary cards and executive KPI displays.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Ingresos
     * 
     * @return array Single-row associative array with structure:
     *         ['TotalVentas' => int, 'TotalIngresos' => float]
     *         Returns zero-valued defaults if no sales data exists
     */
    public static function ingresosTotales() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Ingresos");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['TotalVentas' => 0, 'TotalIngresos' => 0];
    }

    /**
     * juegosMasVendidos()
     * 
     * Retrieves ranked list of best-selling games by transaction count or revenue.
     * Typically top 10-20 items used for marketplace featured/trending sections and
     * inventory focus decisions.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Juegos_Mas_Vendidos
     * 
     * @return array Array of game records ranked by sales volume, structure typically includes:
     *         [
     *             ['JuegoId' => int, 'NombreJuego' => string, 'VentasCount' => int, 
     *              'IngresoGenerado' => float, 'Ranking' => int],
     *             ...
     *         ]
     */
    public static function juegosMasVendidos() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Reporte_Juegos_Mas_Vendidos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

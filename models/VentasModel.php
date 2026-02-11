<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

/**
 * VentasModel
 * 
 * Sales transaction management for administrative oversight. Provides visibility into all
 * completed game key sales across the system and administrative actions to void/reverse
 * transactions if needed.
 * 
 * All operations execute against the remote GBB_Remoto database which maintains the
 * authoritative sales ledger.
 */
class VentasModel {

    /**
     * listarVentas()
     * 
     * Retrieves comprehensive list of all sales transactions system-wide. Used by admin
     * dashboard and sales reports to monitor transaction volume, revenue, and fraud detection.
     * 
     * Includes both direct vendor sales and marketplace sales with complete transaction metadata.
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Ventas_Listar
     * 
     * @return array Array of sales transaction records with structure typically including:
     *         [
     *             ['VentaId' => int, 'Fecha' => string, 'JuegoId' => int, 'ClienteId' => int,
     *              'Monto' => float, 'EstadoVenta' => string, 'KeyId' => int, ...],
     *             ...
     *         ]
     */
    public static function listarVentas() {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Ventas_Listar");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * anularVenta()
     * 
     * Voids/reverses a completed game key sale transaction. This is an administrative function
     * used to handle refunds, disputed charges, or accidental duplicate purchases.
     * 
     * The procedure should:
     * - Mark the sale record with voided/cancelled status
     * - Return the game key to available inventory (Estado = 'DISPONIBLE')
     * - Update customer account credits if applicable
     * - Maintain audit trail of the reversal
     * 
     * SP: [10.26.208.149].GBB_Remoto.dbo.sp_Venta_Anular
     * 
     * @param int $keyId The KeyId from the KeysInventario table to reverse
     * @return bool True if reversal succeeded, false if key not found or already processed
     */
    public static function anularVenta(int $keyId): bool {
        $db = DB::conn();
        $stmt = $db->prepare("EXEC [10.26.208.149].GBB_Remoto.dbo.sp_Venta_Anular @KeyId = :id");
        $stmt->bindValue(':id', $keyId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

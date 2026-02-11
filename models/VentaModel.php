<?php
declare(strict_types=1);

require_once __DIR__ . '/SP.php';

/**
 * VentaModel
 * 
 * Game purchase transaction completion handler. Performs the actual sale operation to allocate
 * a game key to a customer. This is distinct from VentasModel which handles reporting/administration
 * of completed sales.
 * 
 * The purchase flow uses the SP wrapper utility for flexible stored procedure invocation
 * with automatic fallback to alternate procedure names if the primary doesn't exist.
 */
class VentaModel
{
    /**
     * comprarJuego()
     * 
     * Completes a game purchase transaction by allocating an available game key to the customer.
     * The procedure checks inventory availability, decrements stock, and creates the sale record.
     * 
     * RETURN SEMANTICS:
     * - true: Purchase completed successfully, customer received game key
     * - false/string: Purchase failed; string contains user-facing error message
     * 
     * The procedure returns Status field that this method examines:
     * - Status = 'OK' → returns true indicating successful purchase
     * - Status = 'ERROR' (or missing Status) → returns error message string
     * 
     * SP (with fallbacks): [10.26.208.149].GBB_Remoto.dbo.sp_Key_TomarDisponible
     * This is functionally equivalent to sp_Key_Purchase or other naming variants
     * (SP wrapper will attempt fallback names if primary doesn't exist)
     * 
     * @param int $juegoId The JuegoId from GBB_Remoto.dbo.Juegos table (game being purchased)
     * @param int $usuarioId The UsuarioId from GBB_Remoto.dbo.Usuarios table (customer)
     * @return bool|string True if purchase succeeded, string error message if failed
     */
    public static function comprarJuego(int $juegoId, int $usuarioId)
    {
        $res = SP::call('sp_Key_TomarDisponible', [
            'JuegoId' => $juegoId,
            'UsuarioId' => $usuarioId
        ]);

        // Check if procedure returned OK status
        if (($res['Status'] ?? null) !== 'OK') {
            // Return error message string; null defaults to generic message
            return $res['Message'] ?? 'No se pudo completar la compra';
        }

        // Procedure succeeded, customer has been issued a key
        return true;
    }
}

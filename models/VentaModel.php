<?php
declare(strict_types=1);

require_once __DIR__ . '/SP.php';

class VentaModel
{
    public static function comprarJuego(int $juegoId, int $usuarioId)
    {
        $res = SP::call('sp_Key_TomarDisponible', [
            'JuegoId' => $juegoId,
            'UsuarioId' => $usuarioId
        ]);

        if (($res['Status'] ?? null) !== 'OK') {
            return $res['Message'] ?? 'No se pudo completar la compra';
        }

        return true;
    }
}

<div class="modal fade" id="modalCompra" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="carrito_agregar.php" class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirmar compra</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>&iquest;Deseas comprar el juego:</p>
        <h6 id="nombreJuego"></h6>

        <input type="hidden" name="id" id="juegoId">

        <!-- Validacion academica -->
        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" required>
          <label class="form-check-label">
            Confirmo que deseo realizar esta compra
          </label>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-confirm w-100">
            Añadir al carrito
        </button>
      </div>

    </form>
  </div>
</div>

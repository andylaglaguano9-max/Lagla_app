<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cat&aacute;logo | GBB Store</title>
    <?php require_once __DIR__ . '/../partials/theme.php'; ?>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-1: #0b0722;
            --bg-2: #140a3a;
            --bg-3: #1b0f4f;
            --card: rgba(20, 18, 42, 0.85);
            --card-2: rgba(32, 28, 66, 0.95);
            --text: #eef0ff;
            --muted: #b9bdd6;
            --accent: #ffb454;
            --accent-2: #5ad2ff;
            --success: #36d399;
            --danger: #ff6b6b;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            font-family: "Space Grotesk", system-ui, -apple-system, Segoe UI, sans-serif;
            color: var(--text);
            background:
                radial-gradient(800px 380px at 15% 0%, rgba(90, 210, 255, 0.25), transparent 60%),
                radial-gradient(700px 340px at 85% 10%, rgba(255, 180, 84, 0.22), transparent 60%),
                linear-gradient(180deg, var(--bg-3), var(--bg-1));
        }

        .page-shell {
            position: relative;
            overflow: hidden;
        }

        .page-shell::before {
            content: "";
            position: absolute;
            inset: -120px 0 0 0;
            background:
                radial-gradient(500px 260px at 50% -10%, rgba(255, 255, 255, 0.08), transparent 70%),
                radial-gradient(800px 500px at 10% 20%, rgba(90, 210, 255, 0.1), transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .page-shell > * {
            position: relative;
            z-index: 1;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
        }

        .title-block h1 {
            font-size: clamp(28px, 4vw, 40px);
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }

        .title-block p {
            margin: 0;
            color: var(--muted);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(17, 16, 35, 0.6);
            color: var(--text);
            text-decoration: none;
            transition: transform .2s ease, border-color .2s ease, background .2s ease;
        }

        .back-link:hover {
            transform: translateY(-2px);
            border-color: rgba(90, 210, 255, 0.7);
            background: rgba(25, 22, 52, 0.9);
        }

        .filters {
            margin-top: 18px;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(15, 12, 32, 0.6);
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        .filters label {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 6px;
            display: block;
        }
        .filters input,
        .filters select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text);
            outline: none;
        }
        .filters select option {
            background: #120a2e;
            color: #eef0ff;
        }
        .filters select:focus {
            border-color: rgba(90, 210, 255, 0.7);
            box-shadow: 0 0 0 3px rgba(90, 210, 255, 0.15);
        }
        .filters .btn-clear {
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.08);
            color: var(--muted);
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 600;
            cursor: pointer;
        }
        @media (max-width: 900px) {
            .filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 520px) {
            .filters { grid-template-columns: 1fr; }
        }

        .game-cover{height:140px;border-radius:16px;margin-bottom:12px;background:linear-gradient(135deg, rgba(167,139,250,.35) 0%, rgba(236,72,153,.20) 50%, rgba(34,197,94,.18) 100%);} 

        .game-card {
            position: relative;
            background: linear-gradient(160deg, var(--card), var(--card-2));
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px rgba(0, 0, 0, .35);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 60px rgba(0, 0, 0, .45);
            border-color: rgba(90, 210, 255, 0.5);
        }

        .game-title {
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--text);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.45);
        }

        .badge-platform {
            background: rgba(90, 210, 255, 0.18);
            border: 1px solid rgba(90, 210, 255, 0.5);
            color: var(--accent-2);
            font-weight: 600;
            letter-spacing: 0.3px;
            padding: 6px 10px;
        }

        .count-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
        }

        .count-pill strong {
            color: var(--text);
        }

        .stock {
            color: var(--muted);
        }

        .stock strong {
            color: var(--text);
            font-weight: 700;
        }

        .btn-buy {
            background: linear-gradient(120deg, var(--accent), #ff8d4a);
            border: none;
            color: #1d122e;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(255, 180, 84, 0.25);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn-buy:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(255, 180, 84, 0.35);
        }

        .btn-out {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--muted);
            border-radius: 12px;
        }

        .alert {
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(15, 12, 32, 0.8);
            color: var(--text);
        }

        .alert-danger {
            border-color: rgba(255, 107, 107, 0.5);
        }

        .alert-warning {
            border-color: rgba(255, 180, 84, 0.5);
        }

        .modal-content {
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            background: linear-gradient(160deg, rgba(18, 16, 38, 0.95), rgba(30, 26, 56, 0.95));
            color: var(--text);
        }

        .modal-header, .modal-footer {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .btn-confirm {
            background: linear-gradient(120deg, var(--success), #20b982);
            border: none;
            color: #0b1f15;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 16px;
        }

        .form-check-input:checked {
            background-color: var(--accent-2);
            border-color: var(--accent-2);
        }

        @media (max-width: 576px) {
            .page-header { flex-direction: column; align-items: flex-start; }
            .back-link { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>

<div class="container py-5 page-shell">

    <div class="page-header">
        <div class="title-block">
            <h1>Cat&aacute;logo</h1>
            <p>Selecciona tu pr&oacute;ximo juego y revisa el stock disponible.</p>
            <?php if (!empty($juegos)): ?>
                <div class="count-pill">
                    Juegos totales: <strong><?= count($juegos) ?></strong>
                </div>
            <?php endif; ?>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="carrito.php" class="back-link">Carrito (<?= (int)($cartCount ?? 0) ?>)</a>
            <a href="home.php" class="back-link">Volver</a>
        </div>
    </div>

    <div class="filters">
        <div>
            <label for="f-buscar">Buscar</label>
            <input id="f-buscar" type="text" placeholder="Nombre, plataforma, gÃ©nero...">
        </div>
        <div>
            <label for="f-plataforma">Plataforma</label>
            <select id="f-plataforma">
                <option value="">Todas</option>
            </select>
        </div>
        <div>
            <label for="f-genero">GÃ©nero</label>
            <select id="f-genero">
                <option value="">Todos</option>
            </select>
        </div>
        <div>
            <label for="f-precio">Precio mÃ¡x.</label>
            <input id="f-precio" type="number" min="0" step="0.01" placeholder="Ej: 10.00">
        </div>
        <div>
            <button class="btn-clear" type="button" onclick="limpiarFiltros()">Limpiar filtros</button>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger mt-3">
            <?= htmlspecialchars((string)$_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert mt-3">
            <?= htmlspecialchars((string)$_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="row g-4 mt-3">

        <?php if (!empty($juegos)): ?>
            <?php foreach ($juegos as $juego): ?>
                <?php
                    $juegoId = (int)($juego['JuegoId'] ?? 0);
                ?>
                <?php
                    $nombre = $juego['Nombre'] ?? $juego['Juego'] ?? '';
                    $plataforma = $juego['Plataforma'] ?? $juego['NombrePlataforma'] ?? '';
                    $genero = $juego['Genero'] ?? $juego['Categoria'] ?? $juego['Tipo'] ?? '';
                    $precio = $juego['Precio'] ?? 0;
                    $imagen = $juego['ImagenUrl'] ?? $juego['Imagen'] ?? $juego['image'] ?? '';
                ?>
                <div class="col-md-4 col-lg-3 game-item"
                    data-nombre="<?= htmlspecialchars((string)$nombre) ?>"
                    data-plataforma="<?= htmlspecialchars((string)$plataforma) ?>"
                    data-genero="<?= htmlspecialchars((string)$genero) ?>"
                    data-precio="<?= htmlspecialchars((string)$precio) ?>"
                >
                    <div class="card game-card p-3 h-100">
                        <div class="game-cover" style="<?php if ($imagen): ?>background-image:url('<?= htmlspecialchars((string)$imagen) ?>'); background-size:cover; background-position:center;<?php endif; ?>"></div>

                        <span class="badge badge-platform mb-2">
                            <?= htmlspecialchars($juego['Plataforma']) ?>
                        </span>

                        <h5 class="game-title"><?= htmlspecialchars($juego['Nombre']) ?></h5>

                        <p class="mb-2 price">
                            $<?= number_format((float)$juego['Precio'], 2) ?>
                        </p>
                        <?php
                            $stock = $juego['StockDisponible']
                                ?? $juego['Stock']
                                ?? $juego['stock']
                                ?? $juego['Disponibles']
                                ?? $juego['keys']
                                ?? 0;
                        ?>
                        <p class="mb-3 stock">
                            Stock disponible: <strong><?= htmlspecialchars((string)$stock) ?></strong>
                        </p>

                        <div class="d-grid gap-2">
                            <?php $rol = $_SESSION['auth']['Tipo'] ?? ''; ?>
                            <?php if ($rol === 'CLIENTE'): ?>
                                <form method="POST" action="carrito_agregar.php" class="d-grid">
                                    <input type="hidden" name="id" value="<?= (int)$juegoId ?>">
                                    <button class="btn btn-buy w-100" type="submit">AÃ±adir al carrito</button>
                                </form>
                            <?php endif; ?>
                            <button
                                class="btn btn-out w-100 btn-detalles"
                                type="button"
                                onclick="abrirDetalle(<?= (int)$juegoId ?>)"
                            >
                                Detalles
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">
                    No hay juegos disponibles.
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="detalleTitulo">Detalle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Plataforma:</strong> <span id="detallePlataforma"></span></p>
        <p><strong>GÃ©nero:</strong> <span id="detalleGenero"></span></p>
        <p><strong>Precio:</strong> <span id="detallePrecio"></span></p>
        <p id="detalleDescripcion"></p>
      </div>
      <div class="modal-footer border-0">
        <form method="POST" action="carrito_agregar.php" id="detalleComprarForm">
          <input type="hidden" name="id" id="detalleComprarId" value="">
          <button class="btn btn-buy" type="submit">AÃ±adir al carrito</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>

function abrirDetalle(juegoId) {
    if (!juegoId) return;
    fetch('detalle_json.php?id=' + encodeURIComponent(juegoId))
        .then(r => r.json())
        .then(data => {
            if (data.Status !== 'OK') {
                alert(data.Message || 'No se pudo cargar el detalle');
                return;
            }
            const juego = data.Data || {};
            document.getElementById('detalleTitulo').textContent = juego.Nombre || 'Detalle';
            document.getElementById('detallePlataforma').textContent = juego.Plataforma || '-';
            document.getElementById('detalleGenero').textContent = juego.Genero || '-';
            document.getElementById('detallePrecio').textContent = '$' + Number(juego.Precio || 0).toFixed(2);
            document.getElementById('detalleDescripcion').textContent = juego.Descripcion || 'Sin descripciÃ³n';
            document.getElementById('detalleComprarId').value = juego.JuegoId;

            const modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
            modal.show();
        })
        .catch(() => alert('Error al cargar detalle'));
}

const filtroBuscar = document.getElementById('f-buscar');
const filtroPlataforma = document.getElementById('f-plataforma');
const filtroGenero = document.getElementById('f-genero');
const filtroPrecio = document.getElementById('f-precio');
const items = Array.from(document.querySelectorAll('.game-item'));

function poblarSelect(selectEl, values) {
    const unique = Array.from(new Set(values)).filter(v => v);
    unique.sort((a, b) => a.localeCompare(b));
    unique.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v;
        opt.textContent = v;
        selectEl.appendChild(opt);
    });
}

poblarSelect(filtroPlataforma, items.map(i => i.dataset.plataforma));
poblarSelect(filtroGenero, items.map(i => i.dataset.genero));

function aplicarFiltros() {
    const q = (filtroBuscar.value || '').toLowerCase().trim();
    const plat = filtroPlataforma.value;
    const gen = filtroGenero.value;
    const max = parseFloat(filtroPrecio.value || '');

    items.forEach(item => {
        const nombre = (item.dataset.nombre || '').toLowerCase();
        const plataforma = item.dataset.plataforma || '';
        const genero = item.dataset.genero || '';
        const precio = parseFloat(item.dataset.precio || '0');

        const okTexto = !q || nombre.includes(q) || plataforma.toLowerCase().includes(q) || genero.toLowerCase().includes(q);
        const okPlat = !plat || plataforma === plat;
        const okGen = !gen || genero === gen;
        const okPrecio = isNaN(max) || precio <= max;

        item.style.display = (okTexto && okPlat && okGen && okPrecio) ? '' : 'none';
    });
}

function limpiarFiltros() {
    filtroBuscar.value = '';
    filtroPlataforma.value = '';
    filtroGenero.value = '';
    filtroPrecio.value = '';
    aplicarFiltros();
}

['input', 'change'].forEach(evt => {
    filtroBuscar.addEventListener(evt, aplicarFiltros);
    filtroPlataforma.addEventListener(evt, aplicarFiltros);
    filtroGenero.addEventListener(evt, aplicarFiltros);
    filtroPrecio.addEventListener(evt, aplicarFiltros);
});
</script>

</body>
</html>






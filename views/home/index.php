<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GBB Store</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <style>
    :root{
      --bg:#1b1140;
      --bg2:#24135a;
      --card:#22124f;
      --soft:#2b1b66;
      --accent:#7c3aed;
      --accent2:#a78bfa;
      --text:#ffffff;
      --muted:#c8b8ff;
      --line: rgba(255,255,255,.10);
    }
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: radial-gradient(1200px 700px at 20% 0%, #3b1b9a33 0%, transparent 60%),
                  radial-gradient(900px 500px at 80% 20%, #8b5cf633 0%, transparent 60%),
                  linear-gradient(180deg, var(--bg) 0%, #130a31 100%);
      color:var(--text);
    }
    a{color:inherit; text-decoration:none}
    .container{max-width:1200px; margin:0 auto; padding:0 16px}

    /* Top bar */
    .topbar{
      background: linear-gradient(90deg, #4c1d95 0%, #6d28d9 45%, #4c1d95 100%);
      border-bottom: 1px solid var(--line);
    }
    .topbar-inner{
      display:flex; align-items:center; justify-content:space-between;
      padding:10px 0;
      gap:12px;
    }
    .brand{
      display:flex; align-items:center; gap:10px;
      font-weight:800; letter-spacing:.3px;
    }
    .logo{
      width:34px; height:34px; border-radius:10px;
      background: linear-gradient(135deg, #f97316 0%, #ec4899 50%, #22c55e 100%);
      box-shadow: 0 10px 30px rgba(0,0,0,.35);
    }

    .search{
      flex:1;
      display:flex; align-items:center;
      position: relative;
      background: rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.12);
      border-radius: 999px;
      padding:8px 12px;
      gap:10px;
      min-width: 280px;
      max-width: 650px;
    }
    .search.is-invalid{
      border-color: rgba(254,202,202,.9);
      box-shadow: 0 0 0 3px rgba(239,68,68,.25);
    }
    .search input{
      width:100%;
      background:transparent;
      border:0;
      outline:0;
      color:var(--text);
      font-size:14px;
    }
    .search input::placeholder{color:rgba(255,255,255,.7)}
    .search-btn{
      border:0;
      background: rgba(255,255,255,.18);
      color:var(--text);
      font-weight:700;
      padding:8px 12px;
      border-radius: 999px;
      cursor:pointer;
    }
    .search-btn:focus-visible,
    .chip:focus-visible,
    .btn:focus-visible{
      outline: 2px solid rgba(255,255,255,.8);
      outline-offset: 2px;
    }
    .sr-only{
      position:absolute;
      width:1px; height:1px;
      padding:0; margin:-1px;
      overflow:hidden; clip:rect(0,0,0,0);
      white-space:nowrap; border:0;
    }
    .actions{display:flex; align-items:center; gap:10px}
    .role-badge{
      display:inline-flex; align-items:center; gap:6px;
      padding:8px 12px;
      border-radius: 999px;
      border:1px solid rgba(255,255,255,.18);
      background: rgba(255,255,255,.10);
      font-size:12px;
      font-weight:700;
      color:rgba(255,255,255,.92);
      letter-spacing:.3px;
    }
    .chip{
      display:inline-flex; align-items:center; gap:8px;
      padding:10px 12px;
      background: rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.12);
      border-radius: 999px;
      font-size:13px;
      color:rgba(255,255,255,.92);
      cursor:pointer;
    }
    .btn{
      display:inline-flex; align-items:center; justify-content:center;
      padding:10px 14px;
      border-radius: 12px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.10);
      cursor:pointer;
      font-weight:600;
    }
    .btn.primary{
      background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
      border: 0;
      box-shadow: 0 10px 30px rgba(124,58,237,.35);
    }

    /* Nav */
    .nav{
      background: rgba(10, 6, 25, .35);
      border-bottom: 1px solid var(--line);
      backdrop-filter: blur(10px);
    }
    .nav-inner{
      display:flex; gap:14px; flex-wrap:wrap;
      padding:10px 0;
      color:rgba(255,255,255,.88);
      font-size:14px;
      font-weight:600;
      letter-spacing:.2px;
    }
    .nav a{
      padding:8px 10px;
      border-radius: 10px;
      transition: background .2s ease, color .2s ease;
    }
    .nav a:hover{background: rgba(255,255,255,.10); color:#fff}

    /* Hero */
    .hero{
      margin-top:18px;
      background: radial-gradient(900px 400px at 20% 10%, rgba(167,139,250,.35) 0%, transparent 60%),
                  radial-gradient(900px 400px at 80% 40%, rgba(236,72,153,.25) 0%, transparent 60%),
                  linear-gradient(135deg, rgba(255,255,255,.08) 0%, rgba(255,255,255,.04) 100%);
      border:1px solid rgba(255,255,255,.12);
      border-radius: 20px;
      overflow:hidden;
      box-shadow: 0 30px 80px rgba(0,0,0,.35);
    }
    .hero-inner{
      display:grid;
      grid-template-columns: 1.2fr .8fr;
      gap:18px;
      padding:22px;
      align-items:center;
    }
    .hero h1{
      margin:0 0 8px;
      font-size:38px;
      line-height:1.08;
      letter-spacing:.2px;
      text-shadow: 0 10px 30px rgba(0,0,0,.35);
    }
    .hero p{
      margin:0 0 16px;
      color: rgba(255,255,255,.85);
      line-height:1.5;
      font-size:15px;
      max-width: 56ch;
    }
    .hero .cta{
      display:flex; gap:10px; flex-wrap:wrap;
    }
    .hero-art{
      height:220px;
      border-radius: 18px;
      background:
        radial-gradient(140px 120px at 25% 35%, rgba(34,197,94,.25) 0%, transparent 60%),
        radial-gradient(170px 140px at 70% 45%, rgba(236,72,153,.28) 0%, transparent 62%),
        radial-gradient(250px 160px at 50% 70%, rgba(124,58,237,.35) 0%, transparent 70%),
        linear-gradient(135deg, rgba(255,255,255,.10) 0%, rgba(255,255,255,.04) 100%);
      border:1px solid rgba(255,255,255,.10);
      position:relative;
      overflow:hidden;
    }
    .hero-art:before{
      content:"";
      position:absolute; inset:-40px;
      background: conic-gradient(from 180deg, rgba(167,139,250,.25), rgba(236,72,153,.18), rgba(34,197,94,.18), rgba(167,139,250,.25));
      filter: blur(18px);
      opacity:.7;
    }
    .hero-art:after{
      content:"GBB STORE";
      position:absolute; left:18px; bottom:16px;
      font-weight:900;
      letter-spacing:3px;
      color: rgba(255,255,255,.85);
      text-shadow: 0 10px 30px rgba(0,0,0,.5);
    }

    /* Categories row */
    .cats{
      margin-top:16px;
      display:flex; gap:12px; overflow:auto; padding-bottom:6px;
    }
    .cat{
      min-width: 150px;
      padding:14px 14px;
      border-radius: 16px;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.10);
      display:flex; align-items:center; justify-content:space-between;
      gap:10px;
    }
    .cat strong{font-size:14px}
    .cat span{font-size:12px; color: rgba(255,255,255,.72)}
    .badge{
      width:34px; height:34px; border-radius: 12px;
      background: rgba(124,58,237,.30);
      border:1px solid rgba(167,139,250,.35);
      display:grid; place-items:center;
      font-weight:800;
    }

    /* Grid games */
    .section{
      margin-top:22px;
      display:flex; align-items:flex-end; justify-content:space-between;
      gap:12px;
    }
    .section h2{margin:0; font-size:22px}
    .section small{color: rgba(255,255,255,.75)}
    .grid{
      margin-top:12px;
      display:grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap:14px;
    }
    .card{
      border-radius: 18px;
      background: rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.10);
      overflow:hidden;
      display:flex;
      flex-direction:column;
      min-height: 270px;
      box-shadow: 0 18px 40px rgba(0,0,0,.25);
    }
    .thumb{
      background-size:cover;
      background-position:center;
      height:130px;
      background: linear-gradient(135deg, rgba(167,139,250,.35) 0%, rgba(236,72,153,.20) 50%, rgba(34,197,94,.18) 100%);
      position:relative;
    }
    .thumb .tag{
      position:absolute; top:10px; left:10px;
      background: rgba(0,0,0,.35);
      border:1px solid rgba(255,255,255,.15);
      padding:6px 10px;
      border-radius: 999px;
      font-size:12px;
      color: rgba(255,255,255,.92);
      backdrop-filter: blur(8px);
    }
    .content{padding:12px 12px 14px; display:flex; flex-direction:column; gap:10px; flex:1}
    .title{font-weight:800; font-size:14px; line-height:1.2}
    .meta{display:flex; justify-content:space-between; gap:10px; color: rgba(255,255,255,.75); font-size:12px}
    .price{font-weight:900; color:#fff}
    .actions2{margin-top:auto; display:flex; gap:10px}
    .btn2{
      width:100%;
      padding:10px 12px;
      border-radius: 14px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.08);
      cursor:pointer;
      font-weight:700;
      color:#fff;
    }
    .btn2.primary{
      border:0;
      background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
    }

    @media (max-width: 1050px){
      .grid{grid-template-columns: repeat(3, minmax(0, 1fr));}
      .hero-inner{grid-template-columns: 1fr;}
      .hero-art{height:170px}
    }
    @media (max-width: 720px){
      .grid{grid-template-columns: repeat(2, minmax(0, 1fr));}
      .search{min-width: 180px}
      .hero h1{font-size:32px}
    }
    @media (max-width: 460px){
      .grid{grid-template-columns: 1fr;}
    }
  </style>
</head>

<body>
  <!-- TOP BAR -->
  <div class="topbar">
    <div class="container topbar-inner">
      <div class="brand">
        <div class="logo"></div>
        <div>GBB Store</div>
      </div>

      <form class="search" id="top-search" role="search" method="get" action="catalogo.php" title="Búsqueda">
        <label class="sr-only" for="top-search-input">Buscar</label>
        <input id="top-search-input" name="q" type="search" placeholder="Buscar juegos, plataformas y más">
        <button class="search-btn" type="submit">Buscar</button>
      </form>

      <div class="actions">
        <button class="chip" type="button" aria-label="Idioma: Español">ES</button>
        <button class="chip" type="button" aria-label="Moneda: USD">USD</button>
        <a class="chip" href="carrito.php" aria-label="Carrito">
          Carrito (<?= (int)($cartCount ?? 0) ?>)
        </a>
        <div class="role-badge">
          Rol: <?= htmlspecialchars((string)($auth['Tipo'] ?? '')) ?>
        </div>
        <a class="btn" href="logout.php">Salir</a>
      </div>
    </div>
  </div>

  <!-- NAV -->
    <div class="nav">
    <div class="container nav-inner">
      <a href="home.php">Inicio</a>
      <?php $rol = $auth['Tipo'] ?? ''; ?>
      <?php if ($rol === 'ADMIN'): ?>
        <a href="admin/admin_juegos.php">Catálogo</a>
      <?php else: ?>
        <a href="catalogo.php">Catálogo</a>
      <?php endif; ?>
      <?php if ($rol === 'CLIENTE'): ?>
        <a href="ordenes.php">Mis órdenes</a>
        <a href="carrito.php">Carrito</a>
      <?php endif; ?>
      <?php if ($rol === 'VENDEDOR'): ?>
        <a href="vendedor_publicar.php">Publicar key</a>
        <a href="vendedor_publicaciones.php">Mis publicaciones</a>
        <a href="vendedor_ventas.php">Mis ventas</a>
        <a href="vendedor_notificaciones.php">Notificaciones</a>
      <?php endif; ?>
      <?php if ($rol === 'ADMIN'): ?>
        <a href="admin/usuarios.php">Usuarios</a>
        <a href="admin/inventario.php">Inventario</a>
        <a href="admin/ventas.php">Ventas</a>
        <a href="admin/reportes.php">Reportes</a>
        <a href="admin/configuracion.php">Configuración</a>
        <a href="admin/auditoria.php">Auditoría</a>
      <?php endif; ?>
      <a href="perfil.php">Perfil</a>
    </div>
  </div>

  <div class="container">
    <?php if (!empty($error)): ?>
      <div style="margin:12px 0; padding:10px; border-radius:8px; background:#fff1f0; color:#7f1d1d; border:1px solid #fecaca;">
        <strong>Conexión BD:</strong> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    <!-- HERO -->
    <div class="hero">
      <div class="hero-inner">
        <div>
          <h1>Encuentra tu próxima key al mejor precio</h1>
          <p>
            Catálogo central (HOST) con datos distribuidos vía Linked Server.
            Compra segura, auditoría y control por roles.
          </p>
          <div class="cta">
            <a class="btn primary" href="catalogo.php">Explorar catálogo</a>
            <a class="btn" href="#recomendados">Recomendados</a>
          </div>
        </div>
        <div class="hero-art"></div>
      </div>
    </div>

    <!-- CATEGORÍAS (visual) -->
    <div class="cats">
      <div class="cat"><div><strong>Steam</strong><br><span>Keys & Gift Cards</span></div><div class="badge">S</div></div>
      <div class="cat"><div><strong>PlayStation</strong><br><span>PSN & Juegos</span></div><div class="badge">P</div></div>
      <div class="cat"><div><strong>Xbox</strong><br><span>Game Pass</span></div><div class="badge">X</div></div>
      <div class="cat"><div><strong>Nintendo</strong><br><span>eShop</span></div><div class="badge">N</div></div>
      <div class="cat"><div><strong>PC</strong><br><span>Windows / Licencias</span></div><div class="badge">PC</div></div>
    </div>

    <!-- RECOMENDADOS -->
    <div class="section" id="recomendados">
      <div>
        <h2>Catálogo destacado</h2>
        <small>Stock en tiempo real según inventario</small>
      </div>
      <small style="color: rgba(255,255,255,.75);">Total: <?= is_array($juegos) ? count($juegos) : 0 ?></small>
    </div>

    <div class="grid">
      <?php if (empty($juegos)): ?>
        <div style="grid-column: 1 / -1; padding:14px; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10); border-radius:16px;">
          No hay juegos para mostrar aún.
        </div>
      <?php else: ?>
        <?php foreach ($juegos as $row): ?>
          <?php
            // Intento de detectar campos típicos sin romper (porque tu SP puede variar)
            $idJuego = $row['JuegoId'] ?? '';
            $nombre  = $row['Nombre'] ?? 'Juego';
            $plata   = $row['Plataforma'] ?? 'Plataforma';
            $precio  = $row['Precio'] ?? 0;
            $stock   = $row['StockDisponible'] ?? $row['Stock'] ?? $row['Disponibles'] ?? null;
            if ($stock === null) {
              $stock = 0;
            }
            $genero = $row['Genero'] ?? 'Sin género';
            $descripcion = $row['Descripcion'] ?? 'Sin descripción';
            $imagen = $row['ImagenUrl'] ?? $row['Imagen'] ?? $row['image'] ?? '';
          ?>
          <div class="card">
            <div class="thumb" style="<?php if ($imagen): ?>background-image:url('<?= htmlspecialchars((string)$imagen) ?>'); background-size:cover; background-position:center;<?php endif; ?>">
              <div class="tag"><?= htmlspecialchars((string)$plata) ?></div>
            </div>
            <div class="content">
              <div class="title"><?= htmlspecialchars((string)$nombre) ?></div>

              <div class="meta">
                <div><?= htmlspecialchars((string)$genero) ?></div>
                <div class="price">$<?= number_format((float)$precio, 2) ?></div>
              </div>
              <div class="meta">
                <div>Stock: <?= $stock === null ? '—' : htmlspecialchars((string)$stock) ?></div>
              </div>

              <div class="actions2">
                <?php $rol = $_SESSION['auth']['Tipo'] ?? ''; ?>
                <?php if ($rol === 'CLIENTE'): ?>
                  <form method="POST" action="carrito_agregar.php" style="width:100%; margin:0;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string)$idJuego) ?>">
                    <button class="btn2 primary" type="submit">Añadir al carrito</button>
                  </form>
                <?php endif; ?>
                <button class="btn2" type="button"
                onclick="mostrarDetalle(
'<?= htmlspecialchars((string)$nombre) ?>',
'<?= htmlspecialchars((string)$plata) ?>',
'<?= htmlspecialchars((string)$genero) ?>',
'<?= htmlspecialchars((string)$descripcion) ?>',
'<?= number_format((float)$precio, 2) ?>'
)">Detalles</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div style="height:30px"></div>
  </div>
  <div id="modalDetalle" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6);">
    <div style="max-width:420px; margin:10% auto; background:#1f1147; padding:20px; border-radius:16px;">
      <h2 id="mNombre"></h2>
      <p><b>Plataforma:</b> <span id="mPlata"></span></p>
      <p><b>Género:</b> <span id="mGenero"></span></p>
      <p><b>Precio:</b> $<span id="mPrecio"></span></p>
      <p id="mDesc"></p>

      <button class="btn2 primary" onclick="document.getElementById('modalDetalle').style.display='none'">
        Cerrar
      </button>
    </div>
  </div>

  <script>
  function mostrarDetalle(nombre, plataforma, genero, descripcion, precio){
    document.getElementById('mNombre').innerText = nombre;
    document.getElementById('mPlata').innerText = plataforma;
    document.getElementById('mGenero').innerText = genero;
    document.getElementById('mPrecio').innerText = precio;
    document.getElementById('mDesc').innerText = descripcion;
    document.getElementById('modalDetalle').style.display = 'block';
  }

  const searchForm = document.getElementById('top-search');
  const searchInput = document.getElementById('top-search-input');
  if (searchForm && searchInput) {
    searchForm.addEventListener('submit', (e) => {
      const q = searchInput.value.trim();
      if (!q) {
        e.preventDefault();
        searchForm.classList.add('is-invalid');
        searchInput.focus();
      } else {
        searchForm.classList.remove('is-invalid');
      }
    });
    searchInput.addEventListener('input', () => {
      if (searchInput.value.trim()) {
        searchForm.classList.remove('is-invalid');
      }
    });
  }
  </script>
</body>
</html>







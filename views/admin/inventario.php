<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventario</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(20,18,42,.9);--text:#eef0ff;--muted:#b9bdd6;--accent:#ffb454;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.18), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:1200px;margin:40px auto;padding:0 16px;}
    .toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px;}
    .title{font-size:32px;margin:0;}
    .sub{color:var(--muted);margin:6px 0 0;}
    .hero{
      padding:18px;
      border-radius:18px;
      border:1px solid var(--line);
      background:linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
      box-shadow:0 24px 60px rgba(0,0,0,.35);
      display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    }
    .hero .stats{display:flex;gap:10px;flex-wrap:wrap;}
    .stat{
      padding:8px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.06);
      font-size:12px;color:var(--muted);
    }
    .card{margin-top:14px;padding:16px;border-radius:16px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    .alert{margin:12px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    table{width:100%;border-collapse:collapse;margin-top:12px;}
    th,td{padding:12px;border-bottom:1px solid rgba(255,255,255,.12);text-align:left;}
    th{background:rgba(255,255,255,.06);font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);}
    tr:hover td{background:rgba(255,255,255,.03);}
    .pill{padding:4px 10px;border-radius:999px;border:1px solid var(--line);font-size:12px;color:var(--muted);background:rgba(255,255,255,.06);}
    a{color:#5ad2ff;text-decoration:none;}
    .btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:10px 14px;border-radius:12px;border:1px solid var(--line);
      background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;
    }
    .btn.primary{
      border:0;background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;
      box-shadow:0 12px 30px rgba(255,180,84,.25);
    }
    .grid{
      display:grid;grid-template-columns:1.2fr .8fr;gap:16px;
    }
    @media (max-width: 980px){
      .grid{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <div>
        <h1 class="title">Inventario</h1>
        <p class="sub">Stock general de keys por juego y revisión de pendientes.</p>
      </div>
      <div class="stats">
        <div class="stat">Juegos: <?= count($inventario) ?></div>
        <div class="stat">Pendientes: <?= count($pendientes) ?></div>
      </div>
      <div><a href="../home.php" class="btn primary">Volver</a></div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert"><?= htmlspecialchars((string)$_SESSION['flash_success']) ?></div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="grid">
    <div class="card">
      <h2 style="margin:0 0 10px;">Inventario general</h2>
      <div style="max-height:360px; overflow:auto; border-radius:12px;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Juego</th>
            <th>Total</th>
            <th>Disponibles</th>
            <th>Vendidas</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($inventario as $i): ?>
            <tr>
              <td><?= $i['JuegoId'] ?></td>
              <td><?= htmlspecialchars($i['Nombre']) ?></td>
              <td><span class="pill"><?= (int)$i['TotalKeys'] ?></span></td>
              <td><span class="pill"><?= (int)$i['Disponibles'] ?></span></td>
              <td><span class="pill"><?= (int)$i['Vendidas'] ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>

    <div class="card">
      <h2 style="margin:0 0 10px;">Keys pendientes</h2>
      <div style="max-height:360px; overflow:auto; border-radius:12px;">
      <table>
        <thead>
          <tr>
            <th>KeyId</th>
            <th>JuegoId</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendientes as $p): ?>
            <tr>
              <td><?= $p['KeyId'] ?></td>
              <td><?= $p['JuegoId'] ?? '' ?></td>
              <td><span class="pill"><?= htmlspecialchars((string)$p['Estado']) ?></span></td>
              <td>
                <form method="POST" action="inventario.php" style="display:inline;">
                  <input type="hidden" name="keyId" value="<?= (int)$p['KeyId'] ?>">
                  <input type="hidden" name="action" value="aprobar">
                  <button type="submit" class="btn">Aprobar</button>
                </form>
                <form method="POST" action="inventario.php" style="display:inline;">
                  <input type="hidden" name="keyId" value="<?= (int)$p['KeyId'] ?>">
                  <input type="hidden" name="action" value="rechazar">
                  <button type="submit" class="btn">Rechazar</button>
                </form>
                <form method="POST" action="inventario.php" style="display:inline;">
                  <input type="hidden" name="keyId" value="<?= (int)$p['KeyId'] ?>">
                  <input type="hidden" name="action" value="vender">
                  <button type="submit" class="btn">Vender</button>
                </form>
                <form method="POST" action="inventario.php" style="display:inline;">
                  <input type="hidden" name="keyId" value="<?= (int)$p['KeyId'] ?>">
                  <input type="hidden" name="action" value="desactivar">
                  <button type="submit" class="btn">Desactivar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
    </div>
  </div>
</body>
</html>

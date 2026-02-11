<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis ventas</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(16,14,34,.92);--text:#eef0ff;--muted:#b9bdd6;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(900px 480px at 10% -10%, rgba(90,210,255,.22), transparent 60%),radial-gradient(700px 400px at 90% 0%, rgba(155,92,255,.20), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:1000px;margin:40px auto;padding:0 18px;}
    .hero{
      display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;
      padding:16px 18px;border-radius:18px;border:1px solid var(--line);
      background:linear-gradient(135deg, rgba(255,255,255,.10), rgba(255,255,255,.04));
      box-shadow:0 18px 50px rgba(0,0,0,.35);margin-bottom:14px;
    }
    .title{margin:0;font-size:24px;letter-spacing:.2px;}
    .card{padding:18px;border-radius:18px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);}
    .stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:14px 0;}
    .stat{padding:14px;border-radius:16px;border:1px solid var(--line);background:rgba(255,255,255,.06);}
    .stat .label{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.6px;}
    .stat .value{font-size:18px;font-weight:700;margin-top:6px;}
    .cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:12px;}
    .sale-card{padding:14px;border-radius:16px;border:1px solid var(--line);background:rgba(255,255,255,.05);}
    .sale-title{font-weight:700;margin:0 0 6px;}
    .sale-meta{display:flex;justify-content:space-between;gap:10px;color:var(--muted);font-size:12px;}
    .table{width:100%;border-collapse:collapse;margin-top:8px;font-size:14px;}
    .table th,.table td{padding:10px 8px;border-bottom:1px solid var(--line);text-align:left;}
    .table th{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.6px;}
    .muted{color:var(--muted);}
    .alert{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
    @media (max-width: 900px){
      .stats{grid-template-columns:1fr;}
      .cards{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <h1 class="title">Mis ventas</h1>
      <a class="btn" href="home.php">Volver</a>
    </div>
    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>
    <?php
      $totalVentas = is_array($vendedorVentas) ? count($vendedorVentas) : 0;
      $totalIngresos = 0;
      if (!empty($vendedorVentas)) {
        foreach ($vendedorVentas as $row) {
          $totalIngresos += (float)($row['Precio'] ?? 0);
        }
      }
    ?>
    <div class="stats">
      <div class="stat">
        <div class="label">Ventas</div>
        <div class="value"><?= (int)$totalVentas ?></div>
      </div>
      <div class="stat">
        <div class="label">Ingresos</div>
        <div class="value">$<?= number_format((float)$totalIngresos, 2) ?></div>
      </div>
      <div class="stat">
        <div class="label">Promedio</div>
        <div class="value">$<?= $totalVentas > 0 ? number_format((float)($totalIngresos / $totalVentas), 2) : '0.00' ?></div>
      </div>
    </div>

    <div class="card">
      <?php if (empty($vendedorVentas)): ?>
        <div class="muted">Aún no tienes ventas registradas.</div>
      <?php else: ?>
        <div class="cards">
          <?php foreach ($vendedorVentas as $row): ?>
            <div class="sale-card">
              <div class="sale-title"><?= htmlspecialchars((string)($row['Nombre'] ?? '')) ?></div>
              <div class="sale-meta">
                <div>$<?= number_format((float)($row['Precio'] ?? 0), 2) ?></div>
                <div><?= htmlspecialchars((string)($row['FechaVenta'] ?? '')) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <table class="table" style="margin-top:16px;">
          <thead>
            <tr>
              <th>Juego</th>
              <th>Precio</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($vendedorVentas as $row): ?>
              <tr>
                <td><?= htmlspecialchars((string)($row['Nombre'] ?? '')) ?></td>
                <td>$<?= number_format((float)($row['Precio'] ?? 0), 2) ?></td>
                <td><?= htmlspecialchars((string)($row['FechaVenta'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

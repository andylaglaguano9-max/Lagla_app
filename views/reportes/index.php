<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reportes</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(20,18,42,.9);--text:#eef0ff;--muted:#b9bdd6;--accent:#ffb454;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.18), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:1200px;margin:40px auto;padding:0 16px;}
    .toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;}
    .title{font-size:28px;margin:0;}
    .sub{color:var(--muted);margin:4px 0 0;}
    .card{margin-top:14px;padding:16px;border-radius:16px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    .alert{margin:12px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    table{width:100%;border-collapse:collapse;margin-top:12px;}
    th,td{padding:12px;border-bottom:1px solid rgba(255,255,255,.12);text-align:left;}
    th{background:rgba(255,255,255,.06);font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);}
    tr:hover td{background:rgba(255,255,255,.03);}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
    .btn.primary{border:0;background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;box-shadow:0 12px 30px rgba(255,180,84,.25);}
    .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;}
    .stat{padding:12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.06);}
    label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px;}
    input{padding:8px 10px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#fff;}
    @media (max-width: 900px){.grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="container">
    <div class="toolbar">
      <div>
        <h1 class="title">Reportes</h1>
        <p class="sub">Ventas por fecha, ingresos y productos más vendidos.</p>
      </div>
      <div><a href="../home.php" class="btn primary">Volver</a></div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="GET" action="reportes.php" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        <div>
          <label>Fecha inicio</label>
          <input type="date" name="inicio" value="<?= htmlspecialchars($inicio) ?>">
        </div>
        <div>
          <label>Fecha fin</label>
          <input type="date" name="fin" value="<?= htmlspecialchars($fin) ?>">
        </div>
        <button class="btn primary" type="submit">Filtrar</button>
      </form>
    </div>

    <div class="card">
      <div class="grid">
        <div class="stat">
          <div class="sub">Total ventas</div>
          <div style="font-size:22px;font-weight:700;"><?= (int)($ingresos['TotalVentas'] ?? 0) ?></div>
        </div>
        <div class="stat">
          <div class="sub">Total ingresos</div>
          <div style="font-size:22px;font-weight:700;">$<?= number_format((float)($ingresos['TotalIngresos'] ?? 0), 2) ?></div>
        </div>
        <div class="stat">
          <div class="sub">Rango</div>
          <div style="font-size:14px;"><?= htmlspecialchars($inicio) ?> → <?= htmlspecialchars($fin) ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <h2 style="margin:0 0 10px;">Ventas por fecha</h2>
      <table>
        <thead>
          <tr>
            <th>KeyId</th>
            <th>Juego</th>
            <th>Vendedor</th>
            <th>Precio</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ventas as $v): ?>
            <tr>
              <td><?= $v['KeyId'] ?></td>
              <td><?= htmlspecialchars($v['Juego']) ?></td>
              <td><?= htmlspecialchars($v['Vendedor']) ?></td>
              <td>$<?= number_format((float)$v['Precio'], 2) ?></td>
              <td><?= htmlspecialchars((string)$v['FechaVenta']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h2 style="margin:0 0 10px;">Juegos más vendidos</h2>
      <table>
        <thead>
          <tr>
            <th>Juego</th>
            <th>Total vendidas</th>
            <th>Ingresos</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($masVendidos as $m): ?>
            <tr>
              <td><?= htmlspecialchars($m['Juego']) ?></td>
              <td><?= (int)$m['TotalVendidas'] ?></td>
              <td>$<?= number_format((float)$m['Ingresos'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

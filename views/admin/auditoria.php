<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Auditoría</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(20,18,42,.9);--text:#eef0ff;--muted:#b9bdd6;--accent:#8b5cf6;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),radial-gradient(700px 340px at 85% 10%, rgba(139,92,246,.22), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:1200px;margin:40px auto;padding:0 16px;}
    .hero{
      padding:18px;border-radius:18px;border:1px solid var(--line);
      background:linear-gradient(135deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
      box-shadow:0 24px 60px rgba(0,0,0,.35);
      display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    }
    .title{font-size:32px;margin:0;}
    .sub{color:var(--muted);margin:6px 0 0;}
    .stats{display:flex;gap:10px;flex-wrap:wrap;}
    .stat{padding:8px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.06);font-size:12px;color:var(--muted);}
    .btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:10px 14px;border-radius:12px;border:1px solid var(--line);
      background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;
    }
    .btn.primary{
      border:0;background:linear-gradient(120deg, var(--accent), #a78bfa);color:#1d122e;
      box-shadow:0 12px 30px rgba(139,92,246,.25);
    }
    .card{margin-top:14px;padding:16px;border-radius:16px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    .alert{margin:12px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    table{width:100%;border-collapse:collapse;margin-top:12px;}
    th,td{padding:12px;border-bottom:1px solid rgba(255,255,255,.12);text-align:left;vertical-align:top;}
    th{background:rgba(255,255,255,.06);font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);}
    tr:hover td{background:rgba(255,255,255,.03);}
    .pill{padding:4px 10px;border-radius:999px;border:1px solid var(--line);font-size:12px;color:var(--muted);background:rgba(255,255,255,.06);white-space:nowrap;}
    .muted{color:var(--muted);}
    .scroll{max-height:520px; overflow:auto; border-radius:12px;}
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <div>
        <h1 class="title">Auditoría</h1>
        <p class="sub">Registro de eventos del sistema con fecha, usuario, módulo y detalle.</p>
      </div>
      <div class="stats">
        <div class="stat">Eventos: <?= is_array($logs) ? count($logs) : 0 ?></div>
      </div>
      <div><a href="../home.php" class="btn primary">Volver</a></div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
      <h2 style="margin:0 0 10px;">Eventos recientes</h2>
      <div class="scroll">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Usuario</th>
              <th>Acción</th>
              <th>Módulo</th>
              <th>Detalle</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($logs)): ?>
              <tr>
                <td colspan="6" class="muted">No hay eventos registrados todavía.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($logs as $l): ?>
                <tr>
                  <td><?= (int)$l['AuditoriaId'] ?></td>
                  <td><?= htmlspecialchars((string)($l['Usuario'] ?? '')) ?></td>
                  <td><span class="pill"><?= htmlspecialchars((string)$l['Accion']) ?></span></td>
                  <td><?= htmlspecialchars((string)$l['Modulo']) ?></td>
                  <td><?= htmlspecialchars((string)$l['Detalle']) ?></td>
                  <td>
                    <?php
                      $fh = $l['FechaHora'] ?? '';
                      $out = '';
                      if ($fh instanceof DateTime) {
                          $out = $fh->format('d/m/Y H:i');
                      } elseif (is_string($fh) && $fh !== '') {
                          $ts = strtotime($fh);
                          $out = $ts ? date('d/m/Y H:i', $ts) : $fh;
                      }
                      echo htmlspecialchars((string)$out);
                    ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>

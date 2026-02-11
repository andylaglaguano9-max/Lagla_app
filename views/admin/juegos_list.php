<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catálogo (Admin)</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b0722;--bg2:#1b0f4f;--card:rgba(20,18,42,.9);
      --text:#eef0ff;--muted:#b9bdd6;--accent:#ffb454;--accent2:#5ad2ff;
      --danger:#ff6b6b;--line:rgba(255,255,255,.12);
    }
    *{box-sizing:border-box}
    body{
      background:radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),
               radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.18), transparent 60%),
               linear-gradient(180deg, var(--bg2), var(--bg));
      color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;
    }
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
    a{color:var(--accent2);text-decoration:none;}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
    .btn.primary{border:0;background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;box-shadow:0 12px 30px rgba(255,180,84,.25);}
    .pill{padding:4px 10px;border-radius:999px;border:1px solid var(--line);font-size:12px;color:var(--muted);background:rgba(255,255,255,.06);}
    .actions a, .actions button{margin-right:8px;}
    .danger{color:#ff8f8f;}
  </style>
</head>
<body>
  <div class="container">
    <div class="toolbar">
      <div>
        <h1 class="title">Catálogo (Admin)</h1>
        <p class="sub">Agregar, editar y eliminar juegos.</p>
      </div>
      <div>
        <a href="../home.php" class="btn">Volver</a>
        <a href="../juegos_crear.php" class="btn primary">+ Agregar juego</a>
      </div>
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

    <div class="card">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Género</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($juegos as $j): ?>
            <tr>
              <td><?= $j['JuegoId'] ?></td>
              <td><?= htmlspecialchars($j['Nombre']) ?></td>
              <td><span class="pill"><?= htmlspecialchars((string)$j['Genero']) ?></span></td>
              <td>$<?= number_format((float)$j['Precio'], 2) ?></td>
              <td><span class="pill"><?= ($j['Estado'] ?? 0) == 1 ? 'Activo' : 'Inactivo' ?></span></td>
              <td>
                <div class="actions">
                  <a href="../juegos_editar.php?id=<?= (int)$j['JuegoId'] ?>">Editar</a>
                  <form method="POST" action="../juegos_borrar.php" style="display:inline;">
                    <input type="hidden" name="juegoId" value="<?= (int)$j['JuegoId'] ?>">
                    <button type="submit" class="danger" style="background:none;border:none;cursor:pointer;"
                      onclick="return confirm('¿Eliminar definitivamente este juego?');">
                      Eliminar
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

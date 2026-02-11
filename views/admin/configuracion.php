<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Configuración</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(20,18,42,.9);--text:#eef0ff;--muted:#b9bdd6;--accent:#ffb454;--accent2:#5ad2ff;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.18), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:1200px;margin:40px auto;padding:0 16px;}
    .toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:14px;}
    .title{font-size:28px;margin:0;}
    .sub{color:var(--muted);margin:4px 0 0;}
    .card{margin-top:14px;padding:16px;border-radius:16px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    .alert{margin:12px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    label{display:block;margin:10px 0 6px;color:var(--muted);font-size:12px;}
    input,select{width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#fff;}
    select option{background:#120a2e;color:#eef0ff;}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
    .btn.primary{border:0;background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;box-shadow:0 12px 30px rgba(255,180,84,.25);}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
    details{border:1px solid var(--line);border-radius:16px;background:var(--card);padding:12px;}
    summary{cursor:pointer;font-weight:600;list-style:none;}
    summary::-webkit-details-marker{display:none;}
    summary::after{content:"▾";float:right;color:var(--muted);}
    details[open] summary::after{content:"▴";}
    table{width:100%;border-collapse:collapse;margin-top:10px;}
    th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.12);text-align:left;}
    th{background:rgba(255,255,255,.06);font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--muted);}
    @media (max-width: 900px){.grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="container">
    <div class="toolbar">
      <div>
        <h1 class="title">Configuración del sistema</h1>
        <p class="sub">Parámetros generales, plataformas y temas visuales.</p>
      </div>
      <div><a href="../home.php" class="btn primary">Volver</a></div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div class="alert"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="grid">
      <div class="card">
        <h2 style="margin:0 0 6px;">Parámetros del sistema</h2>
        <?php foreach ($parametros as $p): ?>
          <form method="POST" action="configuracion.php">
            <input type="hidden" name="action" value="param_update">
            <input type="hidden" name="parametro" value="<?= htmlspecialchars($p['Parametro']) ?>">
            <label><?= htmlspecialchars($p['Parametro']) ?> <span style="color:#888;">(<?= htmlspecialchars($p['Descripcion'] ?? '') ?>)</span></label>
            <div style="display:flex;gap:8px;">
              <input name="valor" value="<?= htmlspecialchars($p['Valor']) ?>">
              <button class="btn" type="submit">Guardar</button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>

      <div class="card">
        <h2 style="margin:0 0 6px;">Plataformas</h2>
        <form method="POST" action="configuracion.php">
          <input type="hidden" name="action" value="plataforma_create">
          <label>Nuevo nombre</label>
          <div style="display:flex;gap:8px;">
            <input name="nombre" placeholder="Ej: Steam" required>
            <label style="display:flex;align-items:center;gap:6px;margin:0 0 0 4px;">
              <input type="checkbox" name="estado" checked> Activa
            </label>
            <button class="btn" type="submit">Agregar</button>
          </div>
        </form>
        <table>
          <thead>
            <tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Acción</th></tr>
          </thead>
          <tbody>
            <?php foreach ($plataformas as $pl): ?>
              <tr>
                <td><?= $pl['PlataformaId'] ?></td>
                <td><?= htmlspecialchars($pl['Nombre']) ?></td>
                <td><?= ((int)$pl['Estado'] === 1) ? 'Activa' : 'Inactiva' ?></td>
                <td>
                  <form method="POST" action="configuracion.php" style="display:inline;">
                    <input type="hidden" name="action" value="plataforma_update">
                    <input type="hidden" name="plataformaId" value="<?= (int)$pl['PlataformaId'] ?>">
                    <input name="nombre" value="<?= htmlspecialchars($pl['Nombre']) ?>">
                    <label style="display:inline-flex;align-items:center;gap:6px;margin-left:6px;">
                      <input type="checkbox" name="estado" <?= ((int)$pl['Estado'] === 1) ? 'checked' : '' ?>> Activa
                    </label>
                    <button class="btn" type="submit">Actualizar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <details class="card" open>
      <summary>Temas visuales</summary>
      <div style="margin-top:10px;">
        <form method="POST" action="configuracion.php" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
          <input type="hidden" name="action" value="tema_activar">
          <div style="flex:1;min-width:220px;">
            <label>Seleccionar tema</label>
            <select name="temaId">
              <?php foreach ($temas as $t): ?>
                <option value="<?= (int)$t['TemaId'] ?>" <?= ((int)$t['Activo'] === 1) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($t['Nombre']) ?> (<?= htmlspecialchars($t['Fondo']) ?> / <?= htmlspecialchars($t['ColorPrimario']) ?> / <?= htmlspecialchars($t['ColorSecundario']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn primary" type="submit">Activar</button>
        </form>

        <!-- Tabla removida; el selector ya resume la info -->
      </div>
    </details>
  </div>
</body>
</html>

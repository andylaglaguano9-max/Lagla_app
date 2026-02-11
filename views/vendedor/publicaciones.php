<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis publicaciones</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(16,14,34,.92);--text:#eef0ff;--muted:#b9bdd6;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(900px 480px at 10% -10%, rgba(90,210,255,.22), transparent 60%),radial-gradient(700px 400px at 90% 0%, rgba(155,92,255,.20), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:1000px;margin:40px auto;padding:0 18px;}
    .card{padding:18px;border-radius:18px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);}
    .table{width:100%;border-collapse:collapse;margin-top:8px;font-size:14px;}
    .table th,.table td{padding:10px 8px;border-bottom:1px solid var(--line);text-align:left;}
    .table th{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.6px;}
    .status{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;border:1px solid var(--line);}
    .status.pendiente{background:rgba(255,180,84,.12);border-color:rgba(255,180,84,.35);color:#ffe0b2;}
    .status.disponible{background:rgba(90,210,255,.12);border-color:rgba(90,210,255,.35);color:#bdf3ff;}
    .status.vendida{background:rgba(52,211,153,.12);border-color:rgba(52,211,153,.35);color:#c7f9e9;}
    .muted{color:var(--muted);}
    .alert{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    .alert.success{border-color:rgba(52,211,153,.4);background:rgba(52,211,153,.12);color:#c7f9e9;}
    .actions{display:flex;gap:8px;align-items:center;}
    .btn.danger{border-color:rgba(248,113,113,.45);background:rgba(248,113,113,.12);}
    .btn.ghost{background:rgba(255,255,255,.06);}
    .edit-inline{display:flex;justify-content:flex-end;}
    .edit-form{
      margin-top:6px;padding:12px;border-radius:12px;border:1px solid var(--line);
      background:linear-gradient(135deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
      min-width: 280px;
    }
    .edit-form .row{display:flex;gap:8px;flex-wrap:wrap;align-items:center;}
    .edit-form input{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.16);border-radius:10px;padding:8px;color:#fff;outline:none;min-width:140px;}
    .edit-form label{font-size:11px;color:var(--muted);}
    details summary{cursor:pointer;color:#e6dcff;font-size:12px;list-style:none;}
    details summary::-webkit-details-marker{display:none;}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
  </style>
</head>
<body>
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;">
      <h1 style="margin:0;">Mis publicaciones</h1>
      <a class="btn" href="home.php" style="color:#fff;text-decoration:none;">Volver</a>
    </div>
    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert success"><?= htmlspecialchars((string)$_SESSION['flash_success']) ?></div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="card">
      <?php if (empty($vendedorKeys)): ?>
        <div class="muted">No hay publicaciones registradas.</div>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>Juego</th>
              <th>Key</th>
              <th>Precio</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
              <?php foreach ($vendedorKeys as $row): ?>
                <?php
                  $estado = strtoupper((string)($row['Estado'] ?? ''));
                  $estadoClass = 'pendiente';
                  if ($estado === 'DISPONIBLE') { $estadoClass = 'disponible'; }
                  if ($estado === 'VENDIDA') { $estadoClass = 'vendida'; }
                ?>
                <tr>
                  <td><?= htmlspecialchars((string)($row['Nombre'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string)($row['KeyValor'] ?? '')) ?></td>
                  <td>$<?= number_format((float)($row['Precio'] ?? 0), 2) ?></td>
                  <td><span class="status <?= $estadoClass ?>"><?= $estado ?: 'PENDIENTE' ?></span></td>
                  <td>
                    <?php if ($estado === 'PENDIENTE'): ?>
                      <div class="actions">
                        <form method="POST" action="vendedor_publicaciones_accion.php">
                          <input type="hidden" name="accion" value="eliminar">
                          <input type="hidden" name="keyId" value="<?= (int)($row['KeyId'] ?? 0) ?>">
                          <button class="btn danger" type="submit">Eliminar</button>
                        </form>
                        <details class="edit-inline">
                          <summary class="btn ghost">Editar</summary>
                          <div class="edit-form">
                            <form method="POST" action="vendedor_publicaciones_accion.php">
                              <input type="hidden" name="accion" value="actualizar">
                              <input type="hidden" name="keyId" value="<?= (int)($row['KeyId'] ?? 0) ?>">
                              <div class="row" style="margin-bottom:8px;">
                                <div>
                                  <label>Key</label>
                                  <input name="keyValor" value="<?= htmlspecialchars((string)($row['KeyValor'] ?? '')) ?>" placeholder="Key">
                                </div>
                                <div>
                                  <label>Precio</label>
                                  <input name="precio" type="number" step="0.01" min="0" value="<?= number_format((float)($row['Precio'] ?? 0), 2, '.', '') ?>" placeholder="Precio">
                                </div>
                              </div>
                              <button class="btn" type="submit">Guardar cambios</button>
                            </form>
                          </div>
                        </details>
                      </div>
                    <?php else: ?>
                      <span class="muted">Solo pendientes</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

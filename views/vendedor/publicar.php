<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Publicar key</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b0722;
      --bg2:#1b0f4f;
      --card:rgba(18,16,36,.92);
      --text:#eef0ff;
      --muted:#b9bdd6;
      --accent:#9b5cff;
      --accent2:#5ad2ff;
      --line:rgba(255,255,255,.12);
    }
    *{box-sizing:border-box}
    body{background:radial-gradient(900px 480px at 10% -10%, rgba(90,210,255,.22), transparent 60%),radial-gradient(700px 400px at 90% 0%, rgba(155,92,255,.20), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:980px;margin:40px auto;padding:0 18px;}
    .hero{
      display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
      padding:18px 20px;border-radius:18px;border:1px solid var(--line);
      background:
        radial-gradient(120% 120% at 0% 0%, rgba(90,210,255,.14), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.10), rgba(255,255,255,.03));
      box-shadow:0 20px 60px rgba(0,0,0,.35);
      margin-bottom:16px;
    }
    .title{margin:0;font-size:26px;letter-spacing:.2px;}
    .sub{margin:6px 0 0;color:var(--muted);}
    .card{
      padding:20px;border-radius:18px;border:1px solid var(--line);
      background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);
    }
    .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-top:16px;}
    .mini-card{
      padding:14px;border-radius:16px;border:1px solid var(--line);
      background:rgba(255,255,255,.05);
    }
    .mini-title{font-weight:700;margin:0 0 6px;}
    .pill{
      display:inline-flex;align-items:center;gap:6px;
      padding:4px 10px;border-radius:999px;border:1px solid var(--line);
      font-size:12px;color:var(--muted);
    }
    .row{display:flex;gap:12px;flex-wrap:wrap;}
    .field{flex:1 1 240px;display:flex;flex-direction:column;gap:6px;}
    label{font-size:12px;color:var(--muted);}
    input, select{
      background:rgba(255,255,255,.07);
      border:1px solid rgba(255,255,255,.16);
      border-radius:12px;
      padding:12px;
      color:#fff;
      outline:none;
      color-scheme: dark;
    }
    select option{
      background:#1b0f4f;
      color:#fff;
    }
    .btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:10px 16px;border-radius:12px;border:1px solid var(--line);
      background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;
    }
    .btn.primary{
      border:0;background:linear-gradient(120deg, var(--accent), #c08bff);
      color:#1d122e;box-shadow:0 12px 30px rgba(155,92,255,.25);
    }
    .actions{display:flex;justify-content:flex-end;margin-top:12px;}
    .muted{color:var(--muted);}
    .alert{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    .alert.success{border-color:rgba(52,211,153,.4);background:rgba(52,211,153,.12);color:#c7f9e9;}
    .alert.error{border-color:rgba(248,113,113,.4);background:rgba(248,113,113,.12);color:#ffd4d4;}
    .key-list{display:flex;flex-direction:column;gap:10px;margin-top:10px;}
    .key-item{display:flex;justify-content:space-between;gap:10px;align-items:center;}
    .key-meta{color:var(--muted);font-size:12px;}
    @media (max-width: 900px){
      .grid{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <div>
        <h1 class="title">Publicar nueva key</h1>
        <p class="sub">Agrega una key y quedará en revisión hasta aprobación del administrador.</p>
      </div>
      <a class="btn" href="home.php">Volver</a>
    </div>
    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>
    <div id="client-error" class="alert error" style="display:none;"></div>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert error"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert success">Nueva key agregada correctamente.</div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <div class="grid">
      <div class="card">
        <form method="POST" action="vendedor_key_publicar.php" id="publicar-form">
          <div class="row">
            <div class="field">
              <label>Juego</label>
              <select name="juegoId" id="juegoId" required>
                <option value="0">Selecciona un juego</option>
                <?php foreach ($juegos as $j): ?>
                  <option value="<?= (int)($j['JuegoId'] ?? 0) ?>">
                    <?= htmlspecialchars((string)($j['Nombre'] ?? '')) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label>Key</label>
              <input name="keyValor" id="keyValor" placeholder="XXXXX-XXXXX-XXXXX" minlength="10" required>
            </div>
            <div class="field">
              <label>Precio</label>
              <input name="precio" id="precio" type="number" step="0.01" min="0.01" placeholder="9.99" required>
            </div>
          </div>
          <div class="actions">
            <button class="btn primary" type="submit">Publicar</button>
          </div>
        </form>
        <div class="muted" style="margin-top:10px;">
          Estado inicial: <b>PENDIENTE</b>.
        </div>
      </div>

      <div class="card">
        <div class="mini-card">
          <div class="mini-title">Mis keys recientes</div>
          <div class="pill">Últimas publicaciones</div>
          <div class="key-list">
            <?php if (empty($misKeysRecientes)): ?>
              <div class="key-meta">Aún no tienes keys pendientes recientes.</div>
            <?php else: ?>
              <?php foreach ($misKeysRecientes as $k): ?>
                <div class="key-item">
                  <div>
                    <div><?= htmlspecialchars((string)($k['Nombre'] ?? '')) ?></div>
                    <div class="key-meta"><?= htmlspecialchars((string)($k['KeyValor'] ?? '')) ?></div>
                  </div>
                  <div class="key-meta">$<?= number_format((float)($k['Precio'] ?? 0), 2) ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    const form = document.getElementById('publicar-form');
    const errorBox = document.getElementById('client-error');
    const juegoId = document.getElementById('juegoId');
    const keyValor = document.getElementById('keyValor');
    const precio = document.getElementById('precio');

    if (form) {
      form.addEventListener('submit', (e) => {
        const errors = [];
        if (!juegoId || parseInt(juegoId.value, 10) <= 0) {
          errors.push('Selecciona un juego válido.');
        }
        if (!keyValor || keyValor.value.trim().length < 10) {
          errors.push('La key debe tener al menos 10 caracteres.');
        }
        if (!precio || parseFloat(precio.value) <= 0) {
          errors.push('El precio debe ser mayor a 0.');
        }
        if (errors.length) {
          e.preventDefault();
          errorBox.textContent = errors.join(' ');
          errorBox.style.display = 'block';
          errorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      });
    }
  </script>
</body>
</html>

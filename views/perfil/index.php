<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Perfil</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b0722;
      --bg2:#1b0f4f;
      --card:rgba(16,14,34,.92);
      --text:#eef0ff;
      --muted:#b9bdd6;
      --accent:#9b5cff;
      --accent2:#5ad2ff;
      --line:rgba(255,255,255,.12);
    }
    *{box-sizing:border-box}
    body{
      background:
        radial-gradient(900px 480px at 10% -10%, rgba(90,210,255,.22), transparent 60%),
        radial-gradient(700px 400px at 90% 0%, rgba(155,92,255,.20), transparent 60%),
        linear-gradient(180deg, var(--bg2), var(--bg));
      color:var(--text);
      font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;
      margin:0;
    }
    .container{max-width:1040px;margin:40px auto;padding:0 18px;}
    .hero{
      position:relative;
      padding:22px;
      border-radius:20px;
      border:1px solid var(--line);
      background:
        radial-gradient(120% 120% at 0% 0%, rgba(90,210,255,.18), transparent 55%),
        linear-gradient(135deg, rgba(255,255,255,.10), rgba(255,255,255,.04));
      box-shadow:0 28px 70px rgba(0,0,0,.45);
      display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;
    }
    .hero:after{
      content:"";
      position:absolute;inset:0;border-radius:20px;
      background:conic-gradient(from 180deg, rgba(155,92,255,.10), rgba(90,210,255,.10), rgba(255,180,84,.10), rgba(155,92,255,.10));
      opacity:.35;filter:blur(18px);pointer-events:none;
    }
    .title{font-size:34px;margin:0;letter-spacing:.2px;}
    .sub{color:var(--muted);margin:6px 0 0;}
    .btn{
      display:inline-flex;align-items:center;gap:8px;
      padding:10px 16px;border-radius:12px;border:1px solid var(--line);
      background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;
    }
    .btn.primary{
      border:0;background:linear-gradient(120deg, var(--accent), #c08bff);color:#1d122e;
      box-shadow:0 12px 30px rgba(155,92,255,.25);
    }
    .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-top:18px;}
    .card{
      padding:18px;border-radius:18px;border:1px solid var(--line);
      background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);
    }
    .summary{
      display:grid;
      grid-template-columns:120px 1fr;
      gap:10px 14px;
      margin-top:8px;
    }
    .summary .label{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.6px;}
    .summary .value{font-size:15px;}
    .avatar{
      width:56px;height:56px;border-radius:16px;
      background:linear-gradient(135deg, rgba(155,92,255,.35), rgba(90,210,255,.35));
      border:1px solid var(--line);
      display:grid;place-items:center;
      font-weight:700;
      box-shadow:0 12px 30px rgba(0,0,0,.35);
    }
    .row{display:flex;gap:12px;flex-wrap:wrap;}
    .field{flex:1 1 240px;display:flex;flex-direction:column;gap:6px;}
    label{font-size:12px;color:var(--muted);}
    input{
      background:rgba(255,255,255,.07);
      border:1px solid rgba(255,255,255,.16);
      border-radius:12px;
      padding:12px 12px;
      color:#fff;
      outline:none;
    }
    input:focus{border-color:rgba(90,210,255,.6);box-shadow:0 0 0 3px rgba(90,210,255,.2);}
    .muted{color:var(--muted);}
    .alert{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    .pill{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;border:1px solid var(--line);font-size:12px;color:var(--muted);background:rgba(255,255,255,.06);}
    .badge{
      display:inline-flex;align-items:center;gap:6px;
      padding:6px 10px;border-radius:999px;
      background:rgba(90,210,255,.12);border:1px solid rgba(90,210,255,.35);
      color:#bdf3ff;font-size:12px;font-weight:700;
    }
    @media (max-width: 900px){
      .grid{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <div style="position:relative;z-index:1;">
        <h1 class="title">Perfil</h1>
        <p class="sub">Gestiona tus datos personales y revisa tu rol.</p>
      </div>
      <div style="position:relative;z-index:1;">
        <a href="home.php" class="btn primary">Volver</a>
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

    <div class="grid">
      <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
          <div style="display:flex;align-items:center;gap:12px;">
            <div class="avatar"><?= strtoupper(substr((string)($auth['Nombre'] ?? 'U'),0,1)) ?></div>
            <div>
              <div class="badge">Rol: <?= htmlspecialchars((string)($auth['Tipo'] ?? 'No definido')) ?></div>
              <div style="font-weight:700;font-size:18px;margin-top:6px;">
                <?= htmlspecialchars((string)($auth['Nombre'] ?? '')) ?>
              </div>
              <div class="muted"><?= htmlspecialchars((string)($auth['Email'] ?? '')) ?></div>
            </div>
          </div>
          <div class="pill">ID: <?= htmlspecialchars((string)($auth['UsuarioId'] ?? '')) ?></div>
        </div>
        <div class="summary">
          <div class="label">Nombre</div>
          <div class="value"><?= htmlspecialchars((string)($auth['Nombre'] ?? '')) ?></div>
          <div class="label">Correo</div>
          <div class="value"><?= htmlspecialchars((string)($auth['Email'] ?? '')) ?></div>
          <div class="label">Teléfono</div>
          <div class="value"><?= htmlspecialchars((string)($auth['Telefono'] ?? '')) ?></div>
        </div>
      </div>

      <div class="card" id="editar-perfil">
        <h3 style="margin:0 0 10px;">Editar datos</h3>
        <form method="POST" action="perfil_update.php">
          <div class="row">
            <div class="field">
              <label>Nombre</label>
              <input name="nombre" value="<?= htmlspecialchars((string)($auth['Nombre'] ?? '')) ?>" placeholder="Tu nombre">
            </div>
            <div class="field">
              <label>Correo</label>
              <input name="email" value="<?= htmlspecialchars((string)($auth['Email'] ?? '')) ?>" placeholder="correo@ejemplo.com">
            </div>
            <div class="field">
              <label>Teléfono</label>
              <input name="telefono" value="<?= htmlspecialchars((string)($auth['Telefono'] ?? '')) ?>" placeholder="0999999999">
            </div>
          </div>
          <button class="btn primary" type="submit">Guardar cambios</button>
        </form>
        <div class="muted" style="margin-top:10px;">
          Actualiza tu información y se reflejará en toda la plataforma.
        </div>
      </div>
    </div>

  </div>
</body>
</html>




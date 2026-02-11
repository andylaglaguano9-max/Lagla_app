<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Agregar juego</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(20,18,42,.9);--text:#eef0ff;--muted:#b9bdd6;--accent:#ffb454;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.18), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:900px;margin:40px auto;padding:0 16px;}
    .card{margin-top:14px;padding:18px;border-radius:16px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.35);}
    label{display:block;margin:10px 0 6px;color:var(--muted);font-size:12px;}
    input,select,textarea{width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#fff;}
    textarea{min-height:100px;resize:vertical;}
    select option{background:#120a2e;color:#eef0ff;}
    .row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
    .btn{padding:10px 14px;border-radius:12px;border:0;background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;font-weight:700;cursor:pointer;box-shadow:0 12px 30px rgba(255,180,84,.25);}
    .btn.secondary{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);color:#fff;box-shadow:none;}
    .actions{margin-top:16px;display:flex;gap:10px;}
    .alert{margin:12px 0;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);}
    a{color:#5ad2ff;}
    @media (max-width:700px){.row{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="container">
    <h1>Agregar juego</h1>
    <a href="admin/admin_juegos.php">⬅ Volver</a>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="juegos_guardar.php">
        <div class="row">
          <div>
            <label>Nombre</label>
            <input name="nombre" required>
          </div>
          <div>
            <label>Precio</label>
            <input type="number" step="0.01" name="precio" required>
          </div>
        </div>
        <div class="row">
          <div>
            <label>PlataformaId</label>
            <input type="number" name="plataformaId" required>
          </div>
          <div>
            <label>Género</label>
            <input name="genero" required>
          </div>
        </div>
        <label>Imagen URL</label>
        <input name="imagenUrl">
        <label>Descripción</label>
        <textarea name="descripcion"></textarea>
        <div class="actions">
          <button class="btn" type="submit">Guardar</button>
          <a class="btn secondary" href="admin/admin_juegos.php">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notificaciones</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(16,14,34,.92);--text:#eef0ff;--muted:#b9bdd6;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{background:radial-gradient(900px 480px at 10% -10%, rgba(90,210,255,.22), transparent 60%),radial-gradient(700px 400px at 90% 0%, rgba(155,92,255,.20), transparent 60%),linear-gradient(180deg, var(--bg2), var(--bg));color:var(--text);font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;margin:0;}
    .container{max-width:900px;margin:40px auto;padding:0 18px;}
    .card{padding:18px;border-radius:18px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);}
    .notice{display:flex;flex-direction:column;gap:10px;}
    .notice-item{padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.06);}
    .muted{color:var(--muted);}
    .alert{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
  </style>
</head>
<body>
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px;">
      <h1 style="margin:0;">Notificaciones</h1>
      <a class="btn" href="home.php">Volver</a>
    </div>
    <?php if (!empty($error)): ?>
      <div class="alert"><?= htmlspecialchars((string)$error) ?></div>
    <?php endif; ?>
    <div class="card">
      <div class="notice">
        <?php foreach ($notificaciones as $n): ?>
          <div class="notice-item"><?= htmlspecialchars((string)$n) ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</body>
</html>

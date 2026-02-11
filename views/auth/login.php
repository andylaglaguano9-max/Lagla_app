<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GBB - Login</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#0b0722;
      --bg2:#1b0f4f;
      --card:rgba(20,18,42,.88);
      --text:#eef0ff;
      --muted:#b9bdd6;
      --accent:#ffb454;
      --accent2:#5ad2ff;
    }
    *{box-sizing:border-box}
    body{
      min-height:100vh;
      margin:0;
      display:grid;
      place-items:center;
      font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, sans-serif;
      color:var(--text);
      background:
        radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.25), transparent 60%),
        radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.22), transparent 60%),
        linear-gradient(180deg, var(--bg2), var(--bg));
    }
    .card{
      width:min(460px, 92vw);
      padding:26px;
      border-radius:18px;
      background:linear-gradient(160deg, var(--card), rgba(32,28,66,.95));
      border:1px solid rgba(255,255,255,.12);
      box-shadow:0 20px 50px rgba(0,0,0,.35);
    }
    h2{margin:0 0 6px; font-size:26px}
    .sub{color:var(--muted); margin:0 0 18px}
    label{display:block; font-size:12px; color:var(--muted); margin:12px 0 6px}
    input{
      width:100%;
      padding:12px 14px;
      border-radius:12px;
      border:1px solid rgba(255,255,255,.18);
      background:rgba(255,255,255,.06);
      color:var(--text);
      outline:none;
    }
    .btn{
      width:100%;
      margin-top:16px;
      padding:12px 14px;
      border-radius:12px;
      border:0;
      font-weight:700;
      color:#1d122e;
      background:linear-gradient(120deg, var(--accent), #ff8d4a);
      cursor:pointer;
    }
    .quick{
      margin-top:18px;
      padding-top:12px;
      border-top:1px solid rgba(255,255,255,.12);
      color:var(--muted);
      font-size:13px;
      line-height:1.6;
    }
    .quick strong{color:var(--text)}
  </style>
</head>
<body>
  <div class="card">
    <h2>Login GBB Store</h2>
    <p class="sub">Ingresa con tu correo y contraseña</p>

    <form method="POST" action="do_login.php">
      <label>Email</label>
      <input type="email" name="email" placeholder="correo@ejemplo.com" required>

      <label>Contraseña</label>
      <input type="password" name="password" placeholder="••••••" required>

      <button class="btn" type="submit">Ingresar</button>
    </form>

    <div class="quick">
      <strong>Credenciales rápidas:</strong><br>
      Cliente → cliente@gbb.com / 123456<br>
      Vendedor → vendedor@gbb.com / 123456<br>
      Admin → admin@gbb.com / 123456
    </div>
  </div>
</body>
</html>

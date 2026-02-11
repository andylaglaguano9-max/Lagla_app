<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vendedor</title>
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
    .container{max-width:1000px;margin:40px auto;padding:0 18px;}
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
    .title{font-size:30px;margin:0;letter-spacing:.2px;}
    .sub{color:var(--muted);margin:6px 0 0;}
    .grid{display:grid;grid-template-columns:repeat(2, minmax(0,1fr));gap:16px;margin-top:18px;}
    .card{
      padding:18px;border-radius:18px;border:1px solid var(--line);
      background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);
      transition:transform .18s ease, border-color .18s ease;
    }
    .card:hover{transform:translateY(-3px);border-color:rgba(255,255,255,.22);}
    .card h3{margin:0 0 6px;}
    .card p{margin:0;color:var(--muted);}
    .card a{display:block;color:inherit;text-decoration:none;}
    @media (max-width: 720px){
      .grid{grid-template-columns:1fr;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <div>
        <h1 class="title">Vendedor</h1>
        <p class="sub">Elige a dónde quieres ingresar.</p>
      </div>
      <div>
        <a href="home.php" style="color:#fff;text-decoration:none;">Volver</a>
      </div>
    </div>

    <div class="grid">
      <div class="card">
        <a href="vendedor_publicar.php">
          <h3>Publicar nueva key</h3>
          <p>Sube una key y queda en estado pendiente.</p>
        </a>
      </div>
      <div class="card">
        <a href="vendedor_publicaciones.php">
          <h3>Mis publicaciones</h3>
          <p>Revisa tus keys y su estado.</p>
        </a>
      </div>
      <div class="card">
        <a href="vendedor_ventas.php">
          <h3>Mis ventas</h3>
          <p>Consulta el historial de ventas.</p>
        </a>
      </div>
      <div class="card">
        <a href="vendedor_notificaciones.php">
          <h3>Notificaciones</h3>
          <p>Mensajes básicos sobre tus keys.</p>
        </a>
      </div>
      <div class="card">
        <a href="perfil.php">
          <h3>Editar perfil</h3>
          <p>Actualiza tus datos personales.</p>
        </a>
      </div>
    </div>
  </div>
</body>
</html>


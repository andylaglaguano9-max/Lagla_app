<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Crear usuario</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <style>
    body{background:#0f0a2a;color:#fff;font-family:system-ui,-apple-system,Segoe UI,Arial,sans-serif;margin:0;}
    .container{max-width:900px;margin:40px auto;padding:0 16px;}
    .card{margin-top:14px;padding:16px;border-radius:14px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);}
    label{display:block;margin:10px 0 6px;color:#c8b8ff;font-size:12px;}
    input,select{width:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.06);color:#fff;}
    select option{background:#120a2e;color:#eef0ff;}
    select:focus{border-color:rgba(90,210,255,.7);box-shadow:0 0 0 3px rgba(90,210,255,.15);}
    .row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
    .btn{padding:10px 14px;border-radius:12px;border:0;background:linear-gradient(135deg,#7c3aed,#a78bfa);color:#fff;font-weight:700;cursor:pointer;}
    .btn.secondary{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);}
    .actions{margin-top:16px;display:flex;gap:10px;}
    .alert{margin:12px 0;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);}
    a{color:#8b5cf6;}
    @media (max-width:700px){.row{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="container">
    <h1>Crear usuario</h1>
    <a href="admin/usuarios.php">⬅ Volver</a>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="usuarios_guardar.php">
        <div class="row">
          <div>
            <label>Nombre</label>
            <input name="nombre" required>
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="email" required>
          </div>
        </div>
        <div class="row">
          <div>
            <label>Rol</label>
            <select name="tipo" required>
              <option value="CLIENTE">CLIENTE</option>
              <option value="VENDEDOR">VENDEDOR</option>
              <option value="ADMIN">ADMIN</option>
            </select>
          </div>
          <div>
            <label>Contraseña</label>
            <input type="password" name="password" required>
          </div>
        </div>
        <label><input type="checkbox" name="estado" checked> Activo</label>
        <div class="actions">
          <button class="btn" type="submit">Guardar</button>
          <a class="btn secondary" href="admin/usuarios.php">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

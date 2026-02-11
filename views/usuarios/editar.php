<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Editar usuario</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <style>
    :root{
      --bg:#0b0722;
      --bg2:#1b0f4f;
      --card:rgba(20,18,42,.9);
      --text:#eef0ff;
      --muted:#b9bdd6;
      --accent:#ffb454;
      --accent2:#5ad2ff;
      --line:rgba(255,255,255,.12);
    }
    body{
      background:
        radial-gradient(800px 380px at 15% 0%, rgba(90,210,255,.2), transparent 60%),
        radial-gradient(700px 340px at 85% 10%, rgba(255,180,84,.18), transparent 60%),
        linear-gradient(180deg, var(--bg2), var(--bg));
      color:var(--text);
      font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;
      margin:0;
    }
    .container{max-width:900px;margin:40px auto;padding:0 16px;}
    .card{
      margin-top:14px;padding:18px;border-radius:16px;border:1px solid var(--line);
      background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.35);
    }
    label{display:block;margin:10px 0 6px;color:var(--muted);font-size:12px;}
    input,select{
      width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.18);
      background:rgba(255,255,255,.06);color:#fff;
    }
    select option{background:#120a2e;color:#eef0ff;}
    select:focus,input:focus{border-color:rgba(90,210,255,.7);box-shadow:0 0 0 3px rgba(90,210,255,.15);outline:none;}
    .row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
    .btn{
      padding:10px 14px;border-radius:12px;border:0;
      background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;font-weight:700;cursor:pointer;
      box-shadow:0 12px 30px rgba(255,180,84,.25);
    }
    .btn.secondary{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);color:#fff;box-shadow:none;}
    .actions{margin-top:16px;display:flex;gap:10px;}
    .alert{margin:12px 0;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.08);}
    a{color:var(--accent2);}
    @media (max-width:700px){.row{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="container">
    <h1>Editar usuario</h1>
    <a href="admin/usuarios.php">⬅ Volver</a>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="card">
      <form method="POST" action="usuarios_actualizar.php">
        <input type="hidden" name="usuarioId" value="<?= (int)$usuario['UsuarioId'] ?>">
        <div class="row">
          <div>
            <label>Nombre</label>
            <input name="nombre" value="<?= htmlspecialchars((string)$usuario['Nombre']) ?>" required>
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars((string)$usuario['Email']) ?>" required>
          </div>
        </div>
        <div class="row">
          <div>
            <label>Rol</label>
            <select name="tipo" required>
              <?php $tipo = $usuario['Tipo'] ?? 'CLIENTE'; ?>
              <option value="CLIENTE" <?= $tipo === 'CLIENTE' ? 'selected' : '' ?>>CLIENTE</option>
              <option value="VENDEDOR" <?= $tipo === 'VENDEDOR' ? 'selected' : '' ?>>VENDEDOR</option>
              <option value="ADMIN" <?= $tipo === 'ADMIN' ? 'selected' : '' ?>>ADMIN</option>
            </select>
          </div>
          <div>
            <label>Estado</label>
            <select name="estado">
              <?php $estado = (int)($usuario['Estado'] ?? 1); ?>
              <option value="1" <?= $estado === 1 ? 'selected' : '' ?>>Activo</option>
              <option value="0" <?= $estado === 0 ? 'selected' : '' ?>>Inactivo</option>
            </select>
          </div>
        </div>
        <div class="actions">
          <button class="btn" type="submit">Guardar cambios</button>
          <a class="btn secondary" href="admin/usuarios.php">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

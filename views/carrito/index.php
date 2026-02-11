<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carrito | GBB Store</title>
  <?php require_once __DIR__ . '/../partials/theme.php'; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0b0722;--bg2:#1b0f4f;--card:rgba(16,14,34,.92);--text:#eef0ff;--muted:#b9bdd6;--accent:#ffb454;--line:rgba(255,255,255,.12);}
    *{box-sizing:border-box}
    body{
      margin:0;
      font-family:"Space Grotesk", system-ui, -apple-system, Segoe UI, Arial, sans-serif;
      color:var(--text);
      background:
        radial-gradient(900px 480px at 10% -10%, rgba(90,210,255,.22), transparent 60%),
        radial-gradient(700px 400px at 90% 0%, rgba(255,180,84,.18), transparent 60%),
        linear-gradient(180deg, var(--bg2), var(--bg));
    }
    .container{max-width:1100px;margin:40px auto;padding:0 18px;}
    .hero{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;}
    .card{padding:18px;border-radius:18px;border:1px solid var(--line);background:var(--card);box-shadow:0 20px 50px rgba(0,0,0,.38);}
    .grid{display:grid;grid-template-columns:1.2fr .8fr;gap:16px;margin-top:16px;}
    .table{width:100%;border-collapse:collapse;font-size:14px;}
    .table th,.table td{padding:10px 8px;border-bottom:1px solid var(--line);text-align:left;}
    .table th{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.6px;}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);color:#fff;text-decoration:none;font-weight:600;}
    .btn.primary{border:0;background:linear-gradient(120deg, var(--accent), #ff8d4a);color:#1d122e;font-weight:700;}
    .muted{color:var(--muted);}
    .alert{margin:10px 0;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.08);}
    .alert.error{border-color:rgba(248,113,113,.4);background:rgba(248,113,113,.12);color:#ffd4d4;}
    .alert.success{border-color:rgba(52,211,153,.4);background:rgba(52,211,153,.12);color:#c7f9e9;}
    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:10px;}
    label{font-size:12px;color:var(--muted);}
    input, select{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.16);border-radius:12px;padding:10px;color:#fff;outline:none;}
    select option{background:#120a2e;color:#eef0ff;}
    @media (max-width: 900px){.grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>
  <div class="container">
    <div class="hero">
      <h1>Carrito</h1>
      <a class="btn" href="catalogo.php">Seguir comprando</a>
    </div>

    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert error"><?= htmlspecialchars((string)$_SESSION['flash_error']) ?></div>
      <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert success"><?= htmlspecialchars((string)$_SESSION['flash_success']) ?></div>
      <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="grid">
      <div class="card">
        <?php if (empty($items)): ?>
          <div class="muted">Tu carrito está vacío.</div>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th>Juego</th>
                <th>Plataforma</th>
                <th>Precio</th>
                <th>Cant.</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td><?= htmlspecialchars((string)$it['Nombre']) ?></td>
                  <td><?= htmlspecialchars((string)$it['Plataforma']) ?></td>
                  <td>$<?= number_format((float)$it['Precio'], 2) ?></td>
                  <td><?= (int)$it['Cantidad'] ?></td>
                  <td>
                    <form method="POST" action="carrito_quitar.php">
                      <input type="hidden" name="id" value="<?= (int)$it['JuegoId'] ?>">
                      <button class="btn" type="submit">Quitar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <p style="margin-top:12px;"><strong>Total:</strong> $<?= number_format((float)$total, 2) ?></p>
        <?php endif; ?>
      </div>

      <div class="card">
        <h3>Checkout</h3>
        <?php if (empty($items)): ?>
          <div class="muted">Agrega juegos para continuar.</div>
        <?php else: ?>
          <form method="POST" action="carrito_checkout.php">
            <div class="field">
              <label>Nombre completo</label>
              <input name="nombre" required>
            </div>
            <div class="field">
              <label>Email</label>
              <input name="email" type="email" required>
            </div>
            <div class="field">
              <label>Teléfono</label>
              <input name="telefono">
            </div>
            <div class="field">
              <label>Método de pago</label>
              <select name="metodo" required>
                <option value="">Selecciona</option>
                <option>Tarjeta</option>
                <option>PayPal</option>
                <option>Transferencia</option>
              </select>
            </div>
            <button class="btn primary" type="submit">Pagar</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>


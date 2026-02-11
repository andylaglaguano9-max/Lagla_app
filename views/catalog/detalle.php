<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($juego['Nombre']) ?></title>
    <style>
        body{
            background:#0f0a2a;
            color:#fff;
            font-family:system-ui, -apple-system, Segoe UI, Arial, sans-serif;
            margin:0;
        }
        .container{
            max-width:900px;
            margin:40px auto;
            padding:0 16px;
        }
        .card{
            margin-top:14px;
            padding:16px;
            border-radius:14px;
            border:1px solid rgba(255,255,255,.15);
            background: rgba(255,255,255,.06);
        }
        .price{font-weight:700; font-size:18px;}
        a{color:#8b5cf6;}
    </style>
</head>
<body>
<div class="container">
    <h1><?= htmlspecialchars($juego['Nombre']) ?></h1>
    <div class="card">
        <p><strong>Plataforma:</strong> <?= htmlspecialchars((string)$juego['Plataforma']) ?></p>
        <p class="price"><strong>Precio:</strong> $<?= number_format((float)$juego['Precio'], 2) ?></p>
        <p><?= nl2br(htmlspecialchars($juego['Descripcion'] ?? 'Sin descripciÃ³n')) ?></p>
        <form method="POST" action="carrito_agregar.php" style="display:inline;">
            <input type="hidden" name="id" value="<?= (int)$juego['JuegoId'] ?>">
            <button type="submit">Añadir al carrito</button>
        </form>
        <br><br>
        <a href="catalogo.php">⬅ Volver al catálogo</a>
    </div>
</div>
</body>
</html>


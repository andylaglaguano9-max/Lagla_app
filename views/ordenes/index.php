<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Mis órdenes</title>

<style>
body{
    background:#0f0a2a;
    color:#fff;
    font-family:system-ui, -apple-system, Segoe UI, Arial, sans-serif;
}
.container{
    max-width:1000px;
    margin:40px auto;
    padding:0 16px;
}
.hero{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:14px;
}
.title{
    margin:0;
    font-size:28px;
}
.back{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 14px;
    border-radius:999px;
    border:1px solid rgba(255,255,255,.2);
    background:rgba(255,255,255,.08);
    color:#fff;
    text-decoration:none;
}
.toolbar{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin:12px 0 18px;
}
.search{
    flex:1;
    min-width:220px;
    padding:10px 12px;
    border-radius:12px;
    border:1px solid rgba(255,255,255,.18);
    background:rgba(255,255,255,.06);
    color:#fff;
    outline:none;
}
.section{
    margin-top:16px;
}
.section h2{
    margin:0 0 8px;
    font-size:18px;
    color:#e6dcff;
}
.card{
    border-radius:16px;
    border:1px solid rgba(255,255,255,.12);
    background:rgba(16,14,34,.92);
    box-shadow:0 18px 40px rgba(0,0,0,.35);
    overflow:hidden;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:12px;
    border-bottom:1px solid #2b1b66;
    font-size:14px;
    vertical-align:top;
}
th{
    background:#2b1b66;
    color:#d8ccff;
    text-transform:uppercase;
    letter-spacing:.4px;
    font-size:12px;
}
.badge{
    padding:4px 10px;
    border-radius:999px;
    background:#22c55e;
    color:#0b1f15;
    font-size:12px;
    font-weight:700;
    border:1px solid rgba(255,255,255,.12);
}
.badge.warn{background:#ffb454;}
.badge.info{background:#5ad2ff;}
.alert{
    margin:12px 0;
    padding:10px 12px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.2);
    background: rgba(255,255,255,.08);
}
.alert.error{border-color: rgba(255,107,107,.6);}
.muted{color:#b9bdd6;}
a{color:#8b5cf6;}
.key-btn{
    padding:6px 10px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.2);
    background:rgba(255,255,255,.08);
    color:#fff;
    cursor:pointer;
}
.key-panel{
    display:none;
    margin-top:8px;
    padding:10px;
    border-radius:12px;
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.12);
    font-size:13px;
}
</style>
</head>

<body>
<div class="container">

<div class="hero">
    <h1 class="title">Mis órdenes</h1>
    <a class="back" href="home.php">⬅ Volver</a>
</div>

<?php
$detailsMap = $_SESSION['order_details'] ?? [];
date_default_timezone_set('America/Guayaquil');
function fmtFechaEcuador($fechaRaw): string {
    if (!$fechaRaw) return '';
    try {
        $dt = new DateTime((string)$fechaRaw, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('America/Guayaquil'));
        return $dt->format('Y-m-d H:i');
    } catch (Exception $e) {
        return (string)$fechaRaw;
    }
}
?>

<?php if (!empty($message)): ?>
    <div class="alert error">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if (empty($ordenes) && empty($ordenesAntiguas)): ?>
    <p class="muted">No tienes órdenes registradas.</p>
<?php else: ?>

<div class="toolbar">
    <input class="search" id="ordenes-search" type="search" placeholder="Buscar por juego, estado, ID o fecha...">
</div>

<?php
    $withDetail = [];
    $withoutDetail = [];
    $existingIds = [];

    foreach ($ordenes as $o) {
        $id = $o['OrdenId'] ?? $o['Id'] ?? $o['OrdenID'] ?? '';
        $fechaRaw = $o['Fecha'] ?? $o['FechaOrden'] ?? $o['FechaCreacion'] ?? '';
        $fechaFmt = fmtFechaEcuador($fechaRaw);
        $ts = $fechaFmt ? strtotime($fechaFmt) : 0;
        $total = $o['Total'] ?? $o['Monto'] ?? 0;
        $estado = $o['Estado'] ?? $o['Status'] ?? '';
        $items = $o['Items'] ?? $o['TotalItems'] ?? $o['Cantidad'] ?? '';
        $juegosList = [];
        $keysList = [];

        if (!empty($o['Juegos']) && is_array($o['Juegos'])) {
            foreach ($o['Juegos'] as $j) {
                $juegosList[] = ($j['Nombre'] ?? 'Juego') . ' x' . (int)($j['Cantidad'] ?? 0);
                if (!empty($j['Keys']) && is_array($j['Keys'])) {
                    foreach ($j['Keys'] as $k) {
                        $keysList[] = $k;
                    }
                }
            }
        }

        if (empty($keysList) && !empty($detailsMap[(string)$id])) {
            $det = $detailsMap[(string)$id];
            if (!empty($det['items']) && is_array($det['items'])) {
                $juegosList = [];
                $keysList = [];
                $items = 0;
                foreach ($det['items'] as $it) {
                    $juegosList[] = ($it['Nombre'] ?? 'Juego') . ' x' . (int)($it['Cantidad'] ?? 0);
                    $items += (int)($it['Cantidad'] ?? 0);
                    if (!empty($it['Keys']) && is_array($it['Keys'])) {
                        foreach ($it['Keys'] as $k) {
                            $keysList[] = $k;
                        }
                    }
                }
                if (!empty($det['fecha'])) {
                    $fechaFmt = fmtFechaEcuador($det['fecha']);
                }
                if (!empty($det['total'])) {
                    $total = (float)$det['total'];
                }
            }
        }

        $estadoStr = (string)$estado;
        $estadoUpper = strtoupper($estadoStr);
        $isPaid = in_array($estadoUpper, ['PAGADA', 'PAGO', 'PAGADO'], true);
        $hasKeys = !empty($keysList);
        $hasDetail = !empty($juegosList);

        $row = [
            'id' => $id,
            'fecha_fmt' => $fechaFmt,
            'ts' => $ts ?: 0,
            'total' => (float)$total,
            'estado' => $estadoStr,
            'items' => $items,
            'juegos' => implode(', ', $juegosList),
            'keys' => $keysList,
        ];

        $existingIds[(string)$id] = true;
        if ($hasDetail) {
            if ($hasKeys && !$isPaid) {
                $row['estado'] = 'PAGADA';
            }
            $withDetail[] = $row;
        } else {
            $withoutDetail[] = $row;
        }
    }

    // Compras antiguas (sin detalle)
    foreach ($ordenesAntiguas as $o) {
        $id = $o['OrdenId'] ?? $o['Id'] ?? $o['OrdenID'] ?? '';
        if (isset($existingIds[(string)$id])) continue;
        $fechaRaw = $o['Fecha'] ?? $o['FechaOrden'] ?? $o['FechaCreacion'] ?? '';
        $fechaFmt = fmtFechaEcuador($fechaRaw);
        $ts = $fechaFmt ? strtotime($fechaFmt) : 0;
        $total = $o['Total'] ?? $o['Monto'] ?? 0;
        $estado = $o['Estado'] ?? $o['Status'] ?? 'ABIERTA';
        $items = $o['Items'] ?? $o['TotalItems'] ?? $o['Cantidad'] ?? '';
        $legacyJuegos = '';
        if (!empty($o['Juegos'])) {
            $legacyJuegos = is_array($o['Juegos']) ? implode(', ', $o['Juegos']) : (string)$o['Juegos'];
        } elseif (!empty($o['JuegoNombre'])) {
            $legacyJuegos = (string)$o['JuegoNombre'];
        } elseif (!empty($o['Nombre'])) {
            $legacyJuegos = (string)$o['Nombre'];
        }

        $withoutDetail[] = [
            'id' => $id,
            'fecha_fmt' => $fechaFmt,
            'ts' => $ts ?: 0,
            'total' => (float)$total,
            'estado' => (string)$estado,
            'items' => $items,
            'juegos' => $legacyJuegos,
            'keys' => [],
        ];
    }

    usort($withDetail, function ($a, $b) { return $b['ts'] <=> $a['ts']; });
    usort($withoutDetail, function ($a, $b) { return $b['ts'] <=> $a['ts']; });

    $groupsWith = [];
    foreach ($withDetail as $r) {
        $key = $r['ts'] ? date('Y-m', $r['ts']) : 'Sin fecha';
        $groupsWith[$key][] = $r;
    }
    $groupsWithout = [];
    foreach ($withoutDetail as $r) {
        $key = $r['ts'] ? date('Y-m', $r['ts']) : 'Sin fecha';
        $groupsWithout[$key][] = $r;
    }
?>

<?php if (!empty($groupsWith)): ?>
    <div class="section"><h2>Compras recientes</h2></div>
    <?php foreach ($groupsWith as $month => $list): ?>
        <div class="section">
            <h2><?= $month === 'Sin fecha' ? 'Sin fecha' : date('F Y', strtotime($month . '-01')) ?></h2>
            <div class="card">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Juegos</th>
                        <th>Cant.</th>
                        <th>Clave</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $seq = 1; ?>
                    <?php foreach ($list as $r): ?>
                        <tr class="orden-row"
                            data-id="<?= htmlspecialchars((string)$r['id']) ?>"
                            data-estado="<?= htmlspecialchars(strtoupper($r['estado'])) ?>"
                            data-fecha="<?= htmlspecialchars((string)$r['fecha_fmt']) ?>"
                            data-juegos="<?= htmlspecialchars((string)$r['juegos']) ?>">
                            <td><?= $seq++ ?></td>
                            <td><?= htmlspecialchars((string)$r['id']) ?></td>
                            <td><?= htmlspecialchars((string)$r['fecha_fmt']) ?></td>
                            <td>$<?= number_format((float)($r['total']), 2) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($r['estado']) ?></span></td>
                            <td><?= htmlspecialchars((string)($r['juegos'] ?: '—')) ?></td>
                            <td><?= htmlspecialchars((string)($r['items'] ?? '')) ?></td>
                            <td>
                                <?php if (!empty($r['keys'])): ?>
                                    <button class="key-btn" type="button" onclick="toggleKeys('keys-<?= htmlspecialchars((string)$r['id']) ?>')">Ver clave</button>
                                    <div id="keys-<?= htmlspecialchars((string)$r['id']) ?>" class="key-panel">
                                        <?php foreach ($r['keys'] as $k): ?>
                                            <div><?= htmlspecialchars((string)$k) ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($groupsWithout)): ?>
    <div class="section"><h2>Historial antiguo</h2></div>
    <?php foreach ($groupsWithout as $month => $list): ?>
        <div class="section">
            <h2><?= $month === 'Sin fecha' ? 'Sin fecha' : date('F Y', strtotime($month . '-01')) ?></h2>
            <div class="card">
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Juegos</th>
                        <th>Items</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $seq = 1; ?>
                    <?php foreach ($list as $r): ?>
                        <tr class="orden-row"
                            data-id="<?= htmlspecialchars((string)$r['id']) ?>"
                            data-estado="<?= htmlspecialchars(strtoupper($r['estado'])) ?>"
                            data-fecha="<?= htmlspecialchars((string)$r['fecha_fmt']) ?>"
                            data-juegos="<?= htmlspecialchars((string)$r['juegos']) ?>">
                            <td><?= $seq++ ?></td>
                            <td><?= htmlspecialchars((string)$r['id']) ?></td>
                            <td><?= htmlspecialchars((string)$r['fecha_fmt']) ?></td>
                            <td>$<?= number_format((float)($r['total']), 2) ?></td>
                            <td><span class="badge info"><?= htmlspecialchars($r['estado']) ?></span></td>
                            <td><?= htmlspecialchars((string)($r['juegos'] ?: '—')) ?></td>
                            <td><?= htmlspecialchars((string)($r['items'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php endif; ?>

</div>
<script>
const search = document.getElementById('ordenes-search');
if (search) {
    search.addEventListener('input', () => {
        const q = search.value.toLowerCase().trim();
        document.querySelectorAll('.orden-row').forEach(row => {
            const text = (row.dataset.id + ' ' + row.dataset.estado + ' ' + row.dataset.fecha + ' ' + row.dataset.juegos).toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    });
}
function toggleKeys(id){
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'block') ? 'none' : 'block';
}
</script>
</body>
</html>

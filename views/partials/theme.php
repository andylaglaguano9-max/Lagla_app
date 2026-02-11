<?php
require_once __DIR__ . '/../../models/TemaModel.php';

$tema = TemaModel::temaActivo();
if (!$tema) {
    return;
}

// Simple name-to-hex map
$map = [
    'dark' => '#0b0722',
    'light' => '#f5f5ff',
    'purple' => '#7c3aed',
    'blue' => '#5ad2ff',
    'pink' => '#ec4899',
    'cyan' => '#22d3ee',
    'gray' => '#9ca3af',
];

$fondo = $tema['Fondo'] ?? '';
$primario = $tema['ColorPrimario'] ?? '';
$secundario = $tema['ColorSecundario'] ?? '';

$resolve = function ($val) use ($map) {
    $v = strtolower(trim((string)$val));
    if ($v === '') return null;
    if ($v[0] === '#') return $v;
    return $map[$v] ?? null;
};

$bg = $resolve($fondo);
$accent = $resolve($primario);
$accent2 = $resolve($secundario);
?>
<style>
  :root {
    <?php if ($bg): ?>
    --bg-1: <?= $bg ?>;
    --bg-2: <?= $bg ?>;
    --bg2: <?= $bg ?>;
    <?php endif; ?>
    <?php if ($accent): ?>
    --accent: <?= $accent ?>;
    <?php endif; ?>
    <?php if ($accent2): ?>
    --accent-2: <?= $accent2 ?>;
    --accent2: <?= $accent2 ?>;
    <?php endif; ?>
  }
</style>

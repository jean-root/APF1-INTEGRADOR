<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>📊 Seguimiento de Ventas</h2>
    <p>Avance de tus prospectos a través del proceso de venta, de nuevo a cerrado.</p>
  </div>
</div>

<?php
$etiquetasColumna = [
    'nuevo'           => ['label' => '🆕 Nuevo',            'color' => 'var(--accent)'],
    'contactado'      => ['label' => '📞 Contactado',       'color' => '#3b82f6'],
    'visita_agendada' => ['label' => '📅 Visita agendada',  'color' => '#8b5cf6'],
    'cerrado'         => ['label' => '✅ Cerrado (venta)',   'color' => '#2b7a4b'],
    'perdido'         => ['label' => '❌ Perdido',           'color' => '#dc2626'],
];
?>

<div style="display:grid;grid-template-columns:repeat(5,minmax(220px,1fr));gap:1rem;overflow-x:auto;padding-bottom:.5rem">
  <?php foreach ($etiquetasColumna as $estado => $info): ?>
    <div class="admin-card" style="margin-bottom:0;min-width:220px">
      <div class="admin-card__header" style="border-bottom:3px solid <?= $info['color'] ?>">
        <span class="admin-card__title" style="font-size:.9rem"><?= $info['label'] ?></span>
        <span class="badge badge-gray" style="margin-left:.4rem"><?= count($columnas[$estado]) ?></span>
      </div>
      <div style="padding:.75rem;display:flex;flex-direction:column;gap:.6rem">
        <?php if (empty($columnas[$estado])): ?>
          <p style="color:var(--text-3);font-size:.8rem;text-align:center;padding:1rem 0">Sin prospectos aquí.</p>
        <?php else: ?>
          <?php foreach ($columnas[$estado] as $lead): ?>
            <a href="<?= BASE_URL ?>/panel/mensaje/<?= $lead->id ?>" style="text-decoration:none;color:inherit">
              <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:.7rem .8rem">
                <strong style="font-size:.85rem"><?= htmlspecialchars($lead->nombre) ?></strong>
                <div style="font-size:.75rem;color:var(--text-3);margin-top:.2rem">
                  <?= htmlspecialchars($lead->asunto ?: substr($lead->mensaje, 0, 30)) ?>
                </div>
                <div style="font-size:.7rem;color:var(--text-3);margin-top:.4rem">
                  <?= date('d/m/Y', strtotime($lead->created_at)) ?>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<p style="font-size:.8rem;color:var(--text-3);margin-top:1rem">
  Para mover un prospecto de columna, ábrelo y actualiza su estado desde "Ver / Actualizar".
</p>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

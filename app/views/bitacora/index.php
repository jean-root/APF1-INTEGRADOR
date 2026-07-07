<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>🗂️ Bitácora de Auditoría</h2>
    <p>Últimos <?= count($registros) ?> registro<?= count($registros) !== 1 ? 's' : '' ?> de creación, edición y eliminación en el sistema.</p>
  </div>
</div>

<div class="admin-card">
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Entidad</th><th>Detalle</th><th>IP</th></tr>
    </thead>
    <tbody>
      <?php if (empty($registros)): ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-3)">
            Sin actividad registrada todavía.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($registros as $r): ?>
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">#<?= $r->id ?></td>
          <td style="font-size:.8rem"><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></td>
          <td><strong><?= htmlspecialchars($r->usuario_nombre) ?></strong></td>
          <td>
            <?php
              $badge = match ($r->accion) {
                  'crear'    => 'badge-green',
                  'editar'   => 'badge-blue',
                  'eliminar' => 'badge-danger',
                  default    => 'badge-gray',
              };
            ?>
            <span class="badge <?= $badge ?>"><?= ucfirst($r->accion) ?></span>
          </td>
          <td style="font-size:.87rem"><?= htmlspecialchars(ucfirst($r->entidad)) ?><?= $r->entidad_id ? ' #' . $r->entidad_id : '' ?></td>
          <td style="font-size:.85rem;color:var(--text-3)"><?= htmlspecialchars($r->detalle ?? '—') ?></td>
          <td style="font-size:.78rem;color:var(--text-3)"><?= htmlspecialchars($r->ip ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>🎯 Mis Prospectos</h2>
    <p>Personas que se contactaron por la plataforma y te fueron asignadas como lead.</p>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__header">
    <span class="admin-card__title">Prospectos asignados (<?= count($leads) ?>)</span>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Contacto</th><th>Asunto</th><th>Mensaje</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (empty($leads)): ?>
        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--text-3)">Aún no tienes prospectos asignados.</td></tr>
      <?php else: ?>
        <?php foreach ($leads as $lead):
          $badgeEstado = match ($lead->estado ?? 'nuevo') {
            'nuevo' => 'badge-gold', 'contactado', 'visita_agendada' => 'badge-blue',
            'cerrado' => 'badge-green', 'perdido' => 'badge-danger', default => 'badge-gray',
          };
        ?>
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">#<?= $lead->id ?></td>
          <td>
            <strong><?= htmlspecialchars($lead->nombre) ?></strong>
            <div style="font-size:.75rem;color:var(--text-3)"><?= htmlspecialchars($lead->telefono ?: $lead->email) ?></div>
          </td>
          <td style="font-size:.87rem"><?= htmlspecialchars($lead->asunto ?: '—') ?></td>
          <td style="font-size:.85rem;color:var(--text-3);max-width:260px;overflow-wrap:anywhere"><?= htmlspecialchars(substr($lead->mensaje, 0, 70)) ?><?= strlen($lead->mensaje) > 70 ? '…' : '' ?></td>
          <td><span class="badge <?= $badgeEstado ?>"><?= htmlspecialchars(Mensaje::etiquetaEstado($lead->estado ?? 'nuevo')) ?></span></td>
          <td style="font-size:.8rem"><?= date('d/m/Y', strtotime($lead->created_at)) ?></td>
          <td><a href="<?= BASE_URL ?>/panel/mensaje/<?= $lead->id ?>" class="btn btn-sm btn-dark">Ver / Actualizar</a></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

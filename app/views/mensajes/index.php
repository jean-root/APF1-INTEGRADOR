<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>✉️ Bandeja de Mensajes</h2>
    <p><?= count($mensajes) ?> mensaje<?= count($mensajes) !== 1 ? 's' : '' ?> recibido<?= count($mensajes) !== 1 ? 's' : '' ?>
      <?php if ($noLeidos > 0): ?>
        <span class="badge badge-gold" style="margin-left:.4rem"><?= $noLeidos ?> sin leer</span>
      <?php endif; ?>
    </p>
  </div>
</div>

<div class="admin-card">
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>De</th><th>Asunto</th><th>Teléfono</th><th>Vendedor</th><th>Estado del lead</th><th>Fecha</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (empty($mensajes)): ?>
        <tr>
          <td colspan="8" style="text-align:center;padding:3rem;color:var(--text-3)">
            No hay mensajes de contacto todavía.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($mensajes as $m):
          $badgeEstado = match ($m->estado ?? 'nuevo') {
            'nuevo'           => 'badge-gold',
            'contactado'      => 'badge-blue',
            'visita_agendada' => 'badge-blue',
            'cerrado'         => 'badge-green',
            'perdido'         => 'badge-danger',
            default           => 'badge-gray',
          };
        ?>
        <tr style="<?= !$m->leido ? 'font-weight:600' : '' ?>">
          <td style="color:var(--text-3);font-size:.8rem">#<?= $m->id ?></td>
          <td>
            <strong><?= htmlspecialchars($m->nombre) ?></strong>
            <div style="font-size:.75rem;color:var(--text-3);font-weight:400"><?= htmlspecialchars($m->email) ?></div>
          </td>
          <td style="font-size:.87rem"><?= htmlspecialchars($m->asunto ?: substr($m->mensaje, 0, 40)) ?></td>
          <td style="font-size:.87rem"><?= htmlspecialchars($m->telefono ?: '—') ?></td>
          <td style="font-size:.87rem"><?= $m->vendedor_nombre ? htmlspecialchars($m->vendedor_nombre . ' ' . $m->vendedor_apellido) : '<span style="color:var(--text-3)">Sin asignar</span>' ?></td>
          <td><span class="badge <?= $badgeEstado ?>"><?= htmlspecialchars(Mensaje::etiquetaEstado($m->estado ?? 'nuevo')) ?></span></td>
          <td style="font-size:.8rem"><?= date('d/m/Y H:i', strtotime($m->created_at)) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/mensaje/detalle/<?= $m->id ?>" class="btn btn-sm btn-dark" title="Ver">👁️ Ver</a>
            <a href="<?= BASE_URL ?>/mensaje/eliminar/<?= $m->id ?>" class="btn btn-sm btn-danger btn-delete" style="margin-left:.3rem" title="Eliminar">🗑️</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

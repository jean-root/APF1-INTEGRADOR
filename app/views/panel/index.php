<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>👋 Hola, <?= htmlspecialchars($vendedor->nombre) ?></h2>
    <p>Zona: <?= htmlspecialchars($vendedor->zona ?: 'sin asignar') ?> · Comisión: <?= number_format((float)$vendedor->comision, 2) ?>%</p>
  </div>
</div>

<!-- Resumen del pipeline propio -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card__icon blue">🏠</div>
    <div>
      <div class="stat-card__num"><?= count($propiedades) ?></div>
      <div class="stat-card__lbl">Propiedades asignadas</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon gold">🆕</div>
    <div>
      <div class="stat-card__num"><?= $resumenEstados['nuevo'] ?></div>
      <div class="stat-card__lbl">Leads nuevos</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon purple">📅</div>
    <div>
      <div class="stat-card__num"><?= $resumenEstados['contactado'] + $resumenEstados['visita_agendada'] ?></div>
      <div class="stat-card__lbl">En seguimiento</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon green">✅</div>
    <div>
      <div class="stat-card__num"><?= $resumenEstados['cerrado'] ?></div>
      <div class="stat-card__lbl">Ventas cerradas</div>
    </div>
  </div>
</div>

<!-- Mis leads -->
<div class="admin-card">
  <div class="admin-card__header">
    <span class="admin-card__title">🎯 Mis Leads Asignados</span>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Contacto</th><th>Asunto</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (empty($leads)): ?>
        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-3)">Aún no tienes leads asignados.</td></tr>
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
          <td style="font-size:.87rem"><?= htmlspecialchars($lead->asunto ?: substr($lead->mensaje, 0, 40)) ?></td>
          <td><span class="badge <?= $badgeEstado ?>"><?= htmlspecialchars(Mensaje::etiquetaEstado($lead->estado ?? 'nuevo')) ?></span></td>
          <td style="font-size:.8rem"><?= date('d/m/Y', strtotime($lead->created_at)) ?></td>
          <td><a href="<?= BASE_URL ?>/panel/mensaje/<?= $lead->id ?>" class="btn btn-sm btn-dark">Ver / Actualizar</a></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Mis propiedades -->
<div class="admin-card" style="margin-top:1.5rem">
  <div class="admin-card__header">
    <span class="admin-card__title">🏠 Mis Propiedades</span>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>Título</th><th>Tipo</th><th>Precio</th><th>Estado</th></tr>
    </thead>
    <tbody>
      <?php if (empty($propiedades)): ?>
        <tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-3)">No tienes propiedades asignadas todavía.</td></tr>
      <?php else: ?>
        <?php foreach ($propiedades as $p): ?>
        <tr>
          <td><strong><?= htmlspecialchars($p->titulo) ?></strong></td>
          <td><span class="badge badge-blue"><?= ucfirst($p->tipo) ?></span></td>
          <td><?= Propiedad::formatearPrecio((float)$p->precio) ?></td>
          <td><?= $p->activo ? '<span class="badge badge-green">Activa</span>' : '<span class="badge badge-gray">Inactiva</span>' ?></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

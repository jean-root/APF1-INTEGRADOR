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

<!-- Accesos rápidos a las secciones nuevas -->
<div class="admin-card">
  <div class="admin-card__header">
    <span class="admin-card__title">🎯 Prospectos y seguimiento</span>
  </div>
  <p style="padding:0 0 1rem;color:var(--text-3);font-size:.87rem">
    Revisa el detalle de cada prospecto en <strong>Prospectos</strong>, o su avance visual por etapas en <strong>Seguimiento de Ventas</strong>, ambos en el menú lateral.
  </p>
  <div style="display:flex;gap:.75rem;flex-wrap:wrap">
    <a href="<?= BASE_URL ?>/panel/prospectos" class="btn btn-dark">🎯 Ver mis Prospectos</a>
    <a href="<?= BASE_URL ?>/panel/seguimiento" class="btn btn-dark">📊 Ver Seguimiento de Ventas</a>
  </div>
</div>

<!-- Mis propiedades -->
<div class="admin-card" id="mis-propiedades" style="margin-top:1.5rem;scroll-margin-top:1.5rem">
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

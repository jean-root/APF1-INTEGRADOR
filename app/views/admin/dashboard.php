<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<!-- Stats cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card__icon blue">🏠</div>
    <div>
      <div class="stat-card__num"><?= $totalPropiedades ?></div>
      <div class="stat-card__lbl">Total propiedades</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon green">✅</div>
    <div>
      <div class="stat-card__num"><?= $totalActivas ?></div>
      <div class="stat-card__lbl">Propiedades activas</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon gold">👤</div>
    <div>
      <div class="stat-card__num"><?= $totalVendedores ?></div>
      <div class="stat-card__lbl">Vendedores</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-card__icon purple">✉️</div>
    <div>
      <div class="stat-card__num"><?= $totalMensajes ?></div>
      <div class="stat-card__lbl">Mensajes
        <?php if ($noLeidos > 0): ?>
          <span class="badge badge-gold" style="margin-left:.4rem"><?= $noLeidos ?> nuevos</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Acciones rápidas -->
<div style="display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap">
  <a href="<?= BASE_URL ?>/propiedad/crear" class="btn btn-primary">+ Nueva Propiedad</a>
  <a href="<?= BASE_URL ?>/vendedor/crear" class="btn btn-dark">+ Nuevo Vendedor</a>
</div>

<!-- Últimas propiedades -->
<div class="admin-card">
  <div class="admin-card__header">
    <span class="admin-card__title">📋 Últimas Propiedades Agregadas</span>
    <a href="<?= BASE_URL ?>/propiedad/admin" class="btn btn-sm btn-dark">Ver todas</a>
  </div>
  <table class="data-table">
    <thead>
      <tr>
        <th>Propiedad</th>
        <th>Tipo</th>
        <th>Precio</th>
        <th>Estado</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($ultimasProp)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:2rem">Sin propiedades aún.</td></tr>
      <?php else: ?>
        <?php foreach (array_slice($ultimasProp, 0, 3) as $p): ?>
        <tr>
          <td><strong><?= htmlspecialchars($p->titulo) ?></strong>
              <div style="font-size:.75rem;color:var(--text-3)">📍 <?= htmlspecialchars(substr($p->direccion ?? 'Lima', 0, 40)) ?></div>
          </td>
          <td><span class="badge badge-blue"><?= ucfirst($p->tipo) ?></span></td>
          <td><strong><?= Propiedad::formatearPrecio((float)$p->precio) ?></strong></td>
          <td>
            <?php if ($p->activo): ?>
              <span class="badge badge-green">Activa</span>
            <?php else: ?>
              <span class="badge badge-gray">Inactiva</span>
            <?php endif; ?>
          </td>
          <td style="font-size:.8rem"><?= date('d/m/Y', strtotime($p->created_at)) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/propiedad/editar/<?= $p->id ?>" class="btn btn-sm btn-dark" style="margin-right:.3rem">✏️</a>
            <a href="<?= BASE_URL ?>/propiedad/eliminar/<?= $p->id ?>" class="btn btn-sm btn-danger btn-delete">🗑️</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Últimos mensajes -->
<?php if (!empty($ultimosMensajes)): ?>
<div class="admin-card" style="margin-top:1.5rem">
  <div class="admin-card__header">
    <span class="admin-card__title">✉️ Mensajes de Contacto</span>
  </div>
  <table class="data-table">
    <thead>
      <tr><th>De</th><th>Asunto</th><th>Teléfono</th><th>Estado</th><th>Fecha</th></tr>
    </thead>
    <tbody>
      <?php foreach (array_slice($ultimosMensajes, 0, 3) as $m): ?>
      <tr>
        <td>
          <strong><?= htmlspecialchars($m->nombre) ?></strong>
          <div style="font-size:.75rem;color:var(--text-3)"><?= htmlspecialchars($m->email) ?></div>
        </td>
        <td style="font-size:.85rem"><?= htmlspecialchars(substr($m->asunto ?: $m->mensaje, 0, 50)) ?></td>
        <td style="font-size:.85rem"><?= htmlspecialchars($m->telefono ?: '–') ?></td>
        <td><?= $m->leido ? '<span class="badge badge-gray">Leído</span>' : '<span class="badge badge-gold">Nuevo</span>' ?></td>
        <td style="font-size:.8rem"><?= date('d/m/Y', strtotime($m->created_at)) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

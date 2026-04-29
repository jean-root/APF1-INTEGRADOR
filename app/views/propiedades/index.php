<?php $activePage = 'propiedades'; require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div style="padding-top:80px">
  <!-- Page title -->
  <div style="background:var(--primary);padding:3rem 0;text-align:center">
    <h1 style="font-family:'Playfair Display',serif;color:#fff;font-size:2.2rem;margin-bottom:.4rem">
      Propiedades
    </h1>
    <p style="color:rgba(255,255,255,.65);font-size:.95rem">
      <?= count($propiedades) ?> propiedad<?= count($propiedades) !== 1 ? 'es' : '' ?> disponible<?= count($propiedades) !== 1 ? 's' : '' ?>
    </p>
  </div>

  <section class="section">
    <div class="container">

      <!-- Filtros -->
      <div class="filter-bar">
        <a href="<?= BASE_URL ?>/propiedad"
           class="filter-btn <?= empty($tipoActivo) ? 'active' : '' ?>">Todos</a>
        <a href="<?= BASE_URL ?>/propiedad?tipo=casa"
           class="filter-btn <?= $tipoActivo === 'casa' ? 'active' : '' ?>">Casas</a>
        <a href="<?= BASE_URL ?>/propiedad?tipo=departamento"
           class="filter-btn <?= $tipoActivo === 'departamento' ? 'active' : '' ?>">Departamentos</a>
        <a href="<?= BASE_URL ?>/propiedad?tipo=terreno"
           class="filter-btn <?= $tipoActivo === 'terreno' ? 'active' : '' ?>">Terrenos</a>
        <a href="<?= BASE_URL ?>/propiedad?tipo=local"
           class="filter-btn <?= $tipoActivo === 'local' ? 'active' : '' ?>">Locales</a>
      </div>

      <!-- Grid -->
      <?php if (empty($propiedades)): ?>
        <div style="text-align:center;padding:4rem;color:var(--text-3)">
          <p style="font-size:3rem;margin-bottom:1rem">🏚️</p>
          <p>No hay propiedades disponibles para este filtro.</p>
          <a href="<?= BASE_URL ?>/propiedad" class="btn btn-primary" style="margin-top:1rem;display:inline-flex">Ver todas</a>
        </div>
      <?php else: ?>
      <div class="props-grid">
        <?php foreach ($propiedades as $p): ?>
          <article class="prop-card">
            <div class="prop-card__img">
              <img src="<?= $p->imagen !== 'no-imagen.jpg' ? UPLOAD_URL . htmlspecialchars($p->imagen) : '' ?>"
                   alt="<?= htmlspecialchars($p->titulo) ?>"
                   loading="lazy"
                   onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&q=70'">

              <span class="prop-card__price"><?= Propiedad::formatearPrecio((float)$p->precio) ?></span>
            </div>
            <div class="prop-card__body">
              <h3 class="prop-card__title"><?= htmlspecialchars($p->titulo) ?></h3>

              <div class="prop-card__footer">
                <div class="prop-card__agent">
                  <div class="prop-card__agent-av"><?= strtoupper(substr($p->vendedor_nombre ?? 'H', 0, 1)) ?></div>
                  <?= htmlspecialchars(($p->vendedor_nombre ?? '') . ' ' . ($p->vendedor_apellido ?? '')) ?: 'Hogar Ideal' ?>
                </div>
                <a href="<?= BASE_URL ?>/propiedad/detalle/<?= $p->id ?>" class="btn btn-dark btn-sm">Ver más</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div>
  </section>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

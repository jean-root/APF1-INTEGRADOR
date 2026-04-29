<?php $activePage = 'propiedades'; require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="detail-page">

  <!-- Imagen Hero -->
  <img class="detail-hero"
       src="<?= $propiedad->imagen !== 'no-imagen.jpg' ? UPLOAD_URL . htmlspecialchars($propiedad->imagen) : '' ?>"
       alt="<?= htmlspecialchars($propiedad->titulo) ?>"
       onerror="this.src='https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=80'">

  <div class="detail-body">
    <div class="detail-grid">

      <!-- Columna izquierda: info de la propiedad -->
      <div>
        <span class="badge badge-blue" style="margin-bottom:1rem;display:inline-block">
          <?= ucfirst($propiedad->tipo) ?>
        </span>
        <h1 class="detail-title"><?= htmlspecialchars($propiedad->titulo) ?></h1>
        <p style="color:var(--text-3);margin-bottom:.8rem;display:flex;align-items:center;gap:.4rem">
          📍 <?= htmlspecialchars($propiedad->direccion ?: 'Lima, Perú') ?>
        </p>
        <p class="detail-price"><?= Propiedad::formatearPrecio((float)$propiedad->precio) ?></p>

        <!-- Características técnicas -->
        <div class="detail-features">
          <?php if ($propiedad->habitaciones): ?>
            <div class="detail-feat">🛏 <strong><?= $propiedad->habitaciones ?></strong> habitacione<?= $propiedad->habitaciones > 1 ? 's' : '' ?></div>
          <?php endif; ?>
          <?php if ($propiedad->banos): ?>
            <div class="detail-feat">🚿 <strong><?= $propiedad->banos ?></strong> baño<?= $propiedad->banos > 1 ? 's' : '' ?></div>
          <?php endif; ?>
          <?php if ($propiedad->estacionamientos): ?>
            <div class="detail-feat">🚗 <strong><?= $propiedad->estacionamientos ?></strong> estacionamiento<?= $propiedad->estacionamientos > 1 ? 's' : '' ?></div>
          <?php endif; ?>
          <?php if ($propiedad->metros2): ?>
            <div class="detail-feat">📐 <strong><?= (int)$propiedad->metros2 ?></strong> m²</div>
          <?php endif; ?>
        </div>

        <!-- Descripción -->
        <?php if ($propiedad->descripcion): ?>
        <div style="margin-bottom:2rem">
          <h2 style="font-family:'Playfair Display',serif;font-size:1.25rem;margin-bottom:.75rem">Descripción</h2>
          <p class="detail-desc"><?= nl2br(htmlspecialchars($propiedad->descripcion)) ?></p>
        </div>
        <?php endif; ?>

        <!-- Publicado -->
        <p style="font-size:.8rem;color:var(--text-3);margin-top:1.5rem">
          📅 Publicado el <?= date('d \d\e F \d\e Y', strtotime($propiedad->created_at)) ?>
        </p>
        <div style="margin-top:1.5rem">
          <a href="<?= BASE_URL ?>/propiedad" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">
            ← Volver al catálogo
          </a>
        </div>
      </div>

      <!-- Columna derecha: tarjeta del agente -->
      <div>
        <div class="agent-card">
          <?php if (!empty($propiedad->vendedor_nombre)): ?>
            <div class="agent-card__avatar">
              <?= strtoupper(substr($propiedad->vendedor_nombre, 0, 1)) ?>
            </div>
            <p class="agent-card__name">
              <?= htmlspecialchars($propiedad->vendedor_nombre . ' ' . $propiedad->vendedor_apellido) ?>
            </p>
            <p class="agent-card__tel">📞 <?= htmlspecialchars($propiedad->vendedor_telefono ?: 'Consultar') ?></p>
          <?php else: ?>
            <div class="agent-card__avatar">H</div>
            <p class="agent-card__name">Hogar Ideal Perú</p>
            <p class="agent-card__tel">📞 +51 936 338 196</p>
          <?php endif; ?>

          <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0">
          <p style="font-size:.82rem;font-weight:600;color:var(--text-2);margin-bottom:.75rem">Solicitar información</p>

          <form class="contact-form" action="<?= BASE_URL ?>/contacto" method="POST" data-validate>
            <input type="hidden" name="asunto" value="Consulta sobre: <?= htmlspecialchars($propiedad->titulo) ?>">
            <label>Tu nombre *</label>
            <input type="text" name="nombre" placeholder="Juan García" required>
            <label>Email *</label>
            <input type="email" name="email" placeholder="juan@correo.com" required>
            <label>Teléfono</label>
            <input type="tel" name="telefono" placeholder="+51 999 888 777">
            <label>Mensaje *</label>
            <textarea name="mensaje" required>Hola, me interesa la propiedad "<?= htmlspecialchars($propiedad->titulo) ?>". ¿Podrían contactarme?</textarea>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:.9rem;justify-content:center">
              Enviar consulta →
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

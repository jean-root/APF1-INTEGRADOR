<?php $activePage = 'home'; require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<!-- ══════════════════════════════════
     HERO
══════════════════════════════════ -->
<section class="hero">
  <div class="hero__content">
    <div class="hero__badge fade-up">✦ Propiedades Verificadas Legalmente</div>

    <h1 class="hero__title fade-up-2">
      Encuentra tu <em>hogar ideal</em><br>en el Perú
    </h1>
    <p class="hero__subtitle fade-up-3">
      Asesoría legal, tasación comercial justa y la cartera de propiedades
      más confiable de Lima y provincias.
    </p>

    <div class="hero__actions fade-up-3">
      <a href="<?= BASE_URL ?>/propiedad" class="btn btn-primary">
        🔍 Ver Propiedades
      </a>
      <a href="<?= BASE_URL ?>/contacto" class="btn btn-outline">
        Contáctanos
      </a>
    </div>

    <!-- Buscador rápido -->
    <form class="hero__search fade-up" action="<?= BASE_URL ?>/propiedad" method="GET">
      <select name="tipo">
        <option value="">Todos los tipos</option>
        <option value="casa">🏠 Casa</option>
        <option value="departamento">🏢 Departamento</option>
        <option value="terreno">🌿 Terreno</option>
        <option value="local">🏪 Local Comercial</option>
      </select>
      <input type="text" name="q" placeholder="¿Dónde quieres vivir?" readonly
             onclick="this.removeAttribute('readonly')" style="cursor:pointer">
      <button type="submit">🔍 Buscar</button>
    </form>

    <!-- Stats -->
    <div class="hero__stats">
      <div class="hero__stat">
        <div class="hero__stat-num">
          <span class="counter" data-target="<?= $totalPropied ?>" data-suffix="+">0</span>
        </div>
        <div class="hero__stat-lbl">Propiedades</div>
      </div>
      <div class="hero__stat">
        <div class="hero__stat-num"><span class="counter" data-target="4"    data-suffix="">0</span></div>
        <div class="hero__stat-lbl">Agentes</div>
      </div>
      <div class="hero__stat">
        <div class="hero__stat-num"><span class="counter" data-target="100"  data-suffix="%">0</span></div>
        <div class="hero__stat-lbl">Verificadas</div>
      </div>
      <div class="hero__stat">
        <div class="hero__stat-num"><span class="counter" data-target="2026" data-suffix="">0</span></div>
        <div class="hero__stat-lbl">Año Fundación</div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════
     PROPIEDADES DESTACADAS
══════════════════════════════════ -->
<section class="section">
  <div class="container">
    <div class="section__header">
      <p class="section__tag">✦ Propiedades Destacadas</p>
      <h2 class="section__title">Las mejores opciones del mercado</h2>

    </div>

    <?php if (empty($propiedades)): ?>
      <p style="text-align:center;color:var(--text-3);padding:3rem">
        Aún no hay propiedades publicadas. <a href="<?= BASE_URL ?>/auth/login" style="color:var(--accent)">Ingresa al admin</a> para agregar.
      </p>
    <?php else: ?>
    <div class="props-grid">
      <?php foreach ($propiedades as $p): ?>
        <article class="prop-card fade-up">
          <div class="prop-card__img">
            <img src="<?= $p->imagen !== 'no-imagen.jpg' ? UPLOAD_URL . htmlspecialchars($p->imagen) : BASE_URL . '/img/no-imagen.jpg' ?>"
                 alt="<?= htmlspecialchars($p->titulo) ?>"
                 loading="lazy"
                 onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=600&q=70'">

            <span class="prop-card__price"><?= Propiedad::formatearPrecio((float)$p->precio) ?></span>
          </div>
          <div class="prop-card__body">
            <h3 class="prop-card__title"><?= htmlspecialchars($p->titulo) ?></h3>

            <div class="prop-card__footer">
              <?php if (!empty($p->vendedor_nombre)): ?>
              <div class="prop-card__agent">
                <div class="prop-card__agent-av">
                  <?= strtoupper(substr($p->vendedor_nombre, 0, 1)) ?>
                </div>
                <?= htmlspecialchars($p->vendedor_nombre . ' ' . $p->vendedor_apellido) ?>
              </div>
              <?php else: ?>
              <div class="prop-card__agent">
                <div class="prop-card__agent-av">H</div>
                Hogar Ideal
              </div>
              <?php endif; ?>
              <a href="<?= BASE_URL ?>/propiedad/detalle/<?= $p->id ?>" class="btn btn-dark btn-sm">Ver más</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center;margin-top:2.5rem">
      <a href="<?= BASE_URL ?>/propiedad" class="btn btn-primary">Ver todas las propiedades →</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ══════════════════════════════════
     POR QUÉ ELEGIRNOS
══════════════════════════════════ -->
<section class="section section-alt">
  <div class="container">
    <div class="section__header">
      <p class="section__tag">✦ Nuestras Ventajas</p>
      <h2 class="section__title">¿Por qué elegirnos?</h2>
    </div>
    <div class="features-grid">
      <div class="feature-card fade-up">
        <div class="feature-card__icon">⚖️</div>
        <h3 class="feature-card__title">Auditoría Legal</h3>
        <p class="feature-card__text">Cada propiedad pasa por revisión legal antes de publicarse. Cero sorpresas en el proceso de compra.</p>
      </div>
      <div class="feature-card fade-up-2">
        <div class="feature-card__icon">🏦</div>
        <h3 class="feature-card__title">Gestión Bancaria</h3>
        <p class="feature-card__text">Te acompañamos en la gestión directa con bancos para financiar tu propiedad con las mejores tasas.</p>
      </div>
      <div class="feature-card fade-up-3">
        <div class="feature-card__icon">💎</div>
        <h3 class="feature-card__title">Tasación Justa</h3>
        <p class="feature-card__text">Valuación comercial técnica para que vendas al precio correcto y en el menor tiempo posible.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-card__icon">🗺️</div>
        <h3 class="feature-card__title">Conocemos Lima</h3>
        <p class="feature-card__text">Especialistas en distritos clave de Lima y provincias. Tu zona de interés es nuestra especialidad.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════
     CTA
══════════════════════════════════ -->
<section class="cta-section">
  <div class="container">
    <h2>¿Listo para encontrar tu hogar ideal?</h2>
    <p>Habla con uno de nuestros agentes especializados y recibe asesoría personalizada sin costo.</p>
    <a href="<?= BASE_URL ?>/contacto" class="btn btn-primary" style="font-size:1rem;padding:.9rem 2rem">
      Hablar con un agente →
    </a>
  </div>
</section>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

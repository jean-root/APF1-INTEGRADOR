<?php $activePage = 'contacto'; require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

<div class="contact-page">
  <!-- Encabezado -->
  <div style="background:var(--primary);padding:4rem 0;text-align:center;margin-bottom:0">
    <h1 style="font-family:'Playfair Display',serif;color:#fff;font-size:2.2rem;margin-bottom:.4rem">Contáctanos</h1>
    <p style="color:rgba(255,255,255,.65)">Nuestro equipo te responde en menos de 24 horas</p>
  </div>

  <div class="container">
    <div class="contact-grid">

      <!-- Info de contacto -->
      <div style="padding-top:1rem">
        <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;margin-bottom:1.5rem;color:var(--text)">
          Hablemos sobre tu propiedad
        </h2>
        <p style="color:var(--text-2);margin-bottom:2rem;line-height:1.8">
          Somos especialistas en el mercado inmobiliario de Lima y provincias. 
          Ofrecemos asesoría legal, tasación comercial y gestión bancaria directa 
          para que tu compra o venta sea un éxito.
        </p>

        <div class="contact-info__item">
          <div class="contact-info__icon">📞</div>
          <div>
            <div class="contact-info__title">Teléfono / WhatsApp</div>
            <div class="contact-info__text">+51 936 338 196</div>
          </div>
        </div>
        <div class="contact-info__item">
          <div class="contact-info__icon">✉️</div>
          <div>
            <div class="contact-info__title">Correo electrónico</div>
            <div class="contact-info__text">info@hogarideal.pe</div>
          </div>
        </div>
        <div class="contact-info__item">
          <div class="contact-info__icon">📍</div>
          <div>
            <div class="contact-info__title">Oficina</div>
            <div class="contact-info__text">
              Av. Javier Prado Este 1234, San Isidro<br>
              Lima, Perú
            </div>
          </div>
        </div>
        <div class="contact-info__item">
          <div class="contact-info__icon">🕐</div>
          <div>
            <div class="contact-info__title">Horario de atención</div>
            <div class="contact-info__text">Lun – Sáb: 9:00am – 7:00pm</div>
          </div>
        </div>
      </div>

      <!-- Formulario -->
      <div>
        <div style="background:var(--card);border-radius:var(--radius-lg);padding:2.5rem;border:1px solid var(--border);box-shadow:var(--shadow)">

          <?php if ($exito): ?>
            <div class="alert alert-success" style="margin-bottom:1.5rem">
              ✅ ¡Mensaje enviado! Te contactaremos a la brevedad.
            </div>
          <?php endif; ?>
          <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom:1.5rem">
              ❌ <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <h3 style="font-family:'Playfair Display',serif;font-size:1.25rem;margin-bottom:1.5rem;color:var(--text)">
            Envíanos un mensaje
          </h3>

          <form method="POST" class="form-grid" style="gap:1rem" data-validate>
            <div class="form-group">
              <label>Tu nombre *</label>
              <input type="text" name="nombre" placeholder="Juan García" required>
            </div>
            <div class="form-group">
              <label>Email *</label>
              <input type="email" name="email" placeholder="juan@correo.com" required>
            </div>
            <div class="form-group">
              <label>Teléfono</label>
              <input type="tel" name="telefono" placeholder="+51 999 888 777">
            </div>
            <div class="form-group">
              <label>Asunto</label>
              <input type="text" name="asunto" placeholder="Consulta sobre propiedad">
            </div>
            <div class="form-group form-full">
              <label>Mensaje *</label>
              <textarea name="mensaje" required placeholder="Escribe tu consulta aquí..."></textarea>
            </div>
            <div class="form-full">
              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;padding:.9rem">
                Enviar mensaje →
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>

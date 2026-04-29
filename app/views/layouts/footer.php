</main>

<!-- ──────────────────────────────────
     FOOTER
────────────────────────────────── -->
<footer class="footer">
  <div class="container">
    <div class="footer__grid">

      <div>
        <div class="footer__brand-name">🏠 <span>Hogar Ideal</span> Perú</div>
        <p class="footer__desc">
          Tu plataforma inmobiliaria de confianza. Propiedades verificadas legalmente,
          asesoría comercial justa y gestión directa con entidades bancarias.
        </p>
        <div class="footer__social">
          <a href="#" title="Facebook">f</a>
          <a href="#" title="Instagram">in</a>
          <a href="#" title="LinkedIn">Li</a>
          <a href="#" title="WhatsApp">w</a>
        </div>
      </div>

      <div>
        <p class="footer__col-title">Propiedades</p>
        <ul class="footer__links">
          <li><a href="<?= BASE_URL ?>/propiedad?tipo=casa">Casas</a></li>
          <li><a href="<?= BASE_URL ?>/propiedad?tipo=departamento">Departamentos</a></li>
          <li><a href="<?= BASE_URL ?>/propiedad?tipo=terreno">Terrenos</a></li>
          <li><a href="<?= BASE_URL ?>/propiedad?tipo=local">Locales Comerciales</a></li>
        </ul>
      </div>

      <div>
        <p class="footer__col-title">Empresa</p>
        <ul class="footer__links">
          <li><a href="<?= BASE_URL ?>/">Nosotros</a></li>
          <li><a href="<?= BASE_URL ?>/contacto">Contacto</a></li>
          <li><a href="#">Blog Inmobiliario</a></li>
          <li><a href="#">Trabaja con nosotros</a></li>
        </ul>
      </div>

      <div>
        <p class="footer__col-title">Contacto</p>
        <ul class="footer__links">
          <li><a href="tel:+51936338196">+51 936 338 196</a></li>
          <li><a href="mailto:info@hogarideal.pe">info@hogarideal.pe</a></li>
          <li>Lima, Perú</li>
          <li>Lun–Sáb: 9am – 7pm</li>
        </ul>
      </div>

    </div>
    <div class="footer__bottom">
      <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Todos los derechos reservados. 
         | Desarrollado con ❤️ por <strong>Grupo N° 6 – UTP</strong></p>
    </div>
  </div>
</footer>

<!-- JS -->
<script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>

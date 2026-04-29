// ============================================================
//  HOGAR IDEAL PERÚ – app.js
//  Dark Mode | Mobile Menu | Image Preview | Form Validation
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  // ── 1. DARK MODE ────────────────────────────────────────────
  const html       = document.documentElement;
  const toggleBtn  = document.getElementById('dark-toggle');
  const KEY        = 'theme';

  const applyTheme = (theme) => {
    html.setAttribute('data-theme', theme);
    if (toggleBtn) toggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
    localStorage.setItem(KEY, theme);
  };

  // Cargar tema guardado o preferencia del sistema
  const saved = localStorage.getItem(KEY);
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyTheme(saved || (prefersDark ? 'dark' : 'light'));

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      applyTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  }

  // ── 2. MOBILE MENU ──────────────────────────────────────────
  const hamburger  = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');

  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      const open = mobileMenu.style.display === 'flex';
      mobileMenu.style.display = open ? 'none' : 'flex';
      hamburger.textContent = open ? '☰' : '✕';
    });
  }

  // ── 3. SIDEBAR MOBILE (admin) ────────────────────────────────
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar       = document.querySelector('.sidebar');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  // ── 4. NAVBAR SCROLL EFFECT ──────────────────────────────────
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.style.boxShadow = window.scrollY > 30
        ? '0 4px 24px rgba(0,0,0,.3)'
        : 'none';
    }, { passive: true });
  }

  // ── 5. FILTROS DE PROPIEDADES ────────────────────────────────
  const filterBtns = document.querySelectorAll('.filter-btn[data-tipo]');
  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const tipo = btn.dataset.tipo;
      const url  = new URL(window.location.href);
      if (tipo) {
        url.searchParams.set('tipo', tipo);
      } else {
        url.searchParams.delete('tipo');
      }
      window.location.href = url.toString();
    });
  });

  // ── 6. PREVIEW DE IMAGEN EN FORMULARIOS ──────────────────────
  const imageInput = document.getElementById('imagen');
  const preview    = document.getElementById('preview-img');

  if (imageInput && preview) {
    imageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      // Validar tipo
      const allowed = ['image/jpeg', 'image/png', 'image/webp'];
      if (!allowed.includes(file.type)) {
        showAlert('Solo se permiten imágenes JPG, PNG o WEBP.', 'error');
        this.value = '';
        preview.style.display = 'none';
        return;
      }
      // Validar tamaño (5MB)
      if (file.size > 5 * 1024 * 1024) {
        showAlert('La imagen no puede superar 5 MB.', 'error');
        this.value = '';
        preview.style.display = 'none';
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });

    // Click en upload area
    const uploadArea = document.querySelector('.upload-area');
    if (uploadArea) {
      uploadArea.addEventListener('click', () => imageInput.click());
      uploadArea.addEventListener('dragover', e => {
        e.preventDefault();
        uploadArea.style.borderColor = 'var(--accent)';
      });
      uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '';
      });
      uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.style.borderColor = '';
        const dt = e.dataTransfer;
        if (dt.files.length) {
          imageInput.files = dt.files;
          imageInput.dispatchEvent(new Event('change'));
        }
      });
    }
  }

  // ── 7. CONFIRMACIÓN DE ELIMINACIÓN ──────────────────────────
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')) {
        e.preventDefault();
      }
    });
  });

  // ── 8. COUNTER ANIMATION (estadísticas hero) ─────────────────
  const counters = document.querySelectorAll('.counter');
  if (counters.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(c => observer.observe(c));
  }

  function animateCounter(el) {
    const target   = parseInt(el.dataset.target || el.textContent, 10);
    const duration = 1800;
    const step     = target / (duration / 16);
    let   current  = 0;

    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = Math.floor(current) + (el.dataset.suffix || '');
    }, 16);
  }

  // ── 9. FLASH MESSAGES AUTO-HIDE ──────────────────────────────
  const flashMsg = document.querySelector('.flash-msg');
  if (flashMsg) {
    setTimeout(() => {
      flashMsg.style.opacity = '0';
      flashMsg.style.transform = 'translateY(-10px)';
      flashMsg.style.transition = 'all .4s';
      setTimeout(() => flashMsg.remove(), 400);
    }, 3500);
  }

  // ── 10. FORM VALIDATION (contacto + admin) ──────────────────
  const forms = document.querySelectorAll('form[data-validate]');
  forms.forEach(form => {
    form.addEventListener('submit', e => {
      let valid = true;
      form.querySelectorAll('[required]').forEach(field => {
        field.classList.remove('field-error');
        if (!field.value.trim()) {
          field.classList.add('field-error');
          field.style.borderColor = '#e53e3e';
          valid = false;
        } else {
          field.style.borderColor = '';
        }
      });
      if (!valid) {
        e.preventDefault();
        showAlert('Por favor completa todos los campos requeridos.', 'error');
      }
    });
  });

  // ── HELPER: Show alert ───────────────────────────────────────
  function showAlert(msg, type = 'error') {
    const existing = document.querySelector('.js-alert');
    if (existing) existing.remove();

    const div = document.createElement('div');
    div.className = `alert alert-${type} js-alert fade-up`;
    div.textContent = msg;

    const main = document.querySelector('.admin-content') || document.querySelector('main') || document.body;
    main.prepend(div);

    setTimeout(() => {
      div.style.opacity = '0';
      setTimeout(() => div.remove(), 400);
    }, 4000);
  }
});

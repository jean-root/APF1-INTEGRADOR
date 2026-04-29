<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? APP_NAME) ?></title>
  <meta name="description" content="Hogar Ideal Perú – Tu plataforma inmobiliaria de confianza. Propiedades verificadas en Lima y provincias.">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏠</text></svg>">

  <!-- CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
</head>
<body>

<!-- ──────────────────────────────────
     NAVBAR
────────────────────────────────── -->
<nav class="navbar">
  <a href="<?= BASE_URL ?>/" class="navbar__brand">
    🏠 <span>Hogar Ideal</span> Perú
  </a>

  <ul class="navbar__nav">
    <li><a href="<?= BASE_URL ?>/"               class="<?= ($activePage ?? '') === 'home'       ? 'active' : '' ?>">Inicio</a></li>
    <li><a href="<?= BASE_URL ?>/propiedad"       class="<?= ($activePage ?? '') === 'propiedades'? 'active' : '' ?>">Propiedades</a></li>
    <li><a href="<?= BASE_URL ?>/contacto"        class="<?= ($activePage ?? '') === 'contacto'   ? 'active' : '' ?>">Contacto</a></li>
    <li><a href="<?= BASE_URL ?>/auth/login"      class="navbar__cta">Panel Admin</a></li>
  </ul>


  <button class="navbar__hamburger" id="hamburger" title="Menú">☰</button>
</nav>

<!-- Mobile menu -->
<div class="navbar__mobile" id="mobile-menu" style="display:none">
  <a href="<?= BASE_URL ?>/">Inicio</a>
  <a href="<?= BASE_URL ?>/propiedad">Propiedades</a>
  <a href="<?= BASE_URL ?>/contacto">Contacto</a>
  <a href="<?= BASE_URL ?>/auth/login">Panel Admin</a>
</div>

<!-- Flash message -->
<?php if (!empty($_SESSION['flash'])): ?>
  <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="flash-msg alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>
               fade-up"
       style="position:fixed;top:80px;right:1.5rem;z-index:999;max-width:340px;box-shadow:var(--shadow-lg)">
    <?= $flash['type'] === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>

<main>

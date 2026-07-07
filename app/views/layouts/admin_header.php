<!-- ============================================================
     ADMIN LAYOUT COMPONENT
     Inclúyelo al inicio de cada vista de admin así:
     require_once APP_ROOT . '/app/views/layouts/admin_header.php';
============================================================ -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo ?? 'Admin') ?> – Panel</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
</head>
<body>

<div class="admin-layout">

  <?php $rolUsuario = $_SESSION['usuario_rol'] ?? 'supervisor'; ?>

  <!-- ── SIDEBAR ─────────────────────────── -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
      <div class="sidebar__brand-name">🏠 <span>Hogar Ideal</span> Perú</div>
      <div class="sidebar__sub"><?= match($rolUsuario) { 'admin' => 'Panel TI', 'vendedor' => 'Panel Vendedor', default => 'Panel Supervisor' } ?></div>
    </div>

    <nav class="sidebar__nav">
      <?php if ($rolUsuario === 'vendedor'): ?>
        <p class="sidebar__nav-title">Mi Panel</p>
        <a href="<?= BASE_URL ?>/panel" class="active">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Mis Propiedades y Leads
        </a>
      <?php elseif ($rolUsuario === 'supervisor'): ?>
        <p class="sidebar__nav-title">Principal</p>
        <a href="<?= BASE_URL ?>/admin/dashboard"
           class="<?= strpos($titulo ?? '', 'Dashboard') !== false ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>

        <p class="sidebar__nav-title">Gestión</p>
        <a href="<?= BASE_URL ?>/propiedad/admin"
           class="<?= strpos($titulo ?? '', 'Propiedad') !== false && strpos($titulo ?? '', 'Dashboard') === false ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          Propiedades
        </a>
        <a href="<?= BASE_URL ?>/vendedor"
           class="<?= strpos($titulo ?? '', 'Vendedor') !== false ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
          Vendedores
        </a>
        <a href="<?= BASE_URL ?>/mensaje"
           class="<?= strpos($titulo ?? '', 'Mensaje') !== false ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z" opacity="0"/><path d="M22 6l-10 7L2 6"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
          Mensajes
        </a>
      <?php else: ?>
        <p class="sidebar__nav-title">TI</p>
        <a href="<?= BASE_URL ?>/usuario"
           class="<?= strpos($titulo ?? '', 'Usuario') !== false ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
          Usuarios
        </a>
        <a href="<?= BASE_URL ?>/bitacora"
           class="<?= strpos($titulo ?? '', 'Bitácora') !== false ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6M9 16h6M9 8h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
          Bitácora
        </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar__user">
      <div class="sidebar__user-av">
        <?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'A', 0, 1)) ?>
      </div>
      <div>
        <div class="sidebar__user-name"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin') ?></div>
        <div class="sidebar__user-role"><?= match($rolUsuario) { 'admin' => 'Admin (TI)', 'vendedor' => 'Vendedor', default => 'Supervisor' } ?></div>
      </div>
      <a href="<?= BASE_URL ?>/auth/logout" class="sidebar__logout" title="Cerrar sesión">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
      </a>
    </div>
  </aside>

  <!-- ── MAIN ───────────────────────────── -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div>
        <h1><?= htmlspecialchars($titulo ?? 'Admin') ?></h1>
      </div>
      <div style="display:flex;align-items:center;gap:.75rem">
        <a href="<?= BASE_URL ?>/auth/cambiar" class="btn btn-sm btn-dark">Cambiar clave</a>
        <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-sm btn-danger">Salir</a>
      </div>
    </header>

    <!-- Flash -->
    <?php if (!empty($_SESSION['flash'])): ?>
      <?php $flash = $_SESSION['flash']; unset($_SESSION['flash']); ?>
      <div class="flash-container">
        <div class="flash-msg alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="admin-content">

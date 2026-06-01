<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<?php if (!empty($_SESSION['temp_password'])): ?>
  <div class="alert alert-success" style="margin-bottom:1rem;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
    <span>
      Contraseña temporal para <strong><?= htmlspecialchars($_SESSION['temp_user_email'] ?? '') ?></strong>:
      <strong id="temp-password"><?= htmlspecialchars($_SESSION['temp_password']) ?></strong>
    </span>
    <button type="button" class="btn btn-sm btn-dark" onclick="copyTempPassword()">Copiar</button>
  </div>
  <script>
    function copyTempPassword() {
      const el = document.getElementById('temp-password');
      if (!el) return;
      navigator.clipboard.writeText(el.textContent || '').then(() => {
        alert('Contraseña copiada');
      });
    }
  </script>
  <?php unset($_SESSION['temp_password'], $_SESSION['temp_user_email']); ?>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>👥 Gestión de Usuarios</h2>
    <p><?= count($usuarios) ?> usuario<?= count($usuarios) !== 1 ? 's' : '' ?> registrado<?= count($usuarios) !== 1 ? 's' : '' ?></p>
  </div>
  <a href="<?= BASE_URL ?>/usuario/crear" class="btn btn-primary">+ Nuevo Usuario</a>
</div>

<div class="admin-card">
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Clave</th><th>Registrado</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (empty($usuarios)): ?>
        <tr>
          <td colspan="8" style="text-align:center;padding:3rem;color:var(--text-3)">
            No hay usuarios. <a href="<?= BASE_URL ?>/usuario/crear" style="color:var(--accent)">Agregar primero →</a>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">#<?= $u->id ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:.7rem">
              <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--primary));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0">
                <?= strtoupper(substr($u->nombre, 0, 1)) ?>
              </div>
              <strong><?= htmlspecialchars($u->nombre) ?></strong>
            </div>
          </td>
          <td style="font-size:.87rem"><?= htmlspecialchars($u->email) ?></td>
          <td><span class="badge badge-blue"><?= ucfirst($u->rol) ?></span></td>
          <td>
            <?= (int)$u->estado === 1
              ? '<span class="badge badge-green">Activo</span>'
              : '<span class="badge badge-gray">Inactivo</span>' ?>
          </td>
          <td>
            <?= (int)($u->password_reset_required ?? 1) === 1
              ? '<span class="badge badge-gold">Pendiente</span>'
              : '<span class="badge badge-gray">OK</span>' ?>
          </td>
          <td style="font-size:.8rem"><?= date('d/m/Y', strtotime($u->created_at)) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/usuario/editar/<?= $u->id ?>" class="btn btn-sm btn-dark" title="Editar">✏️ Editar</a>
            <?php if (($u->rol ?? '') !== 'admin'): ?>
              <a href="<?= BASE_URL ?>/usuario/reset/<?= $u->id ?>" class="btn btn-sm" style="background:#2f3b42;color:#fff;margin-left:.3rem" title="Reiniciar contraseña">🔑</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/usuario/eliminar/<?= $u->id ?>" class="btn btn-sm btn-danger btn-delete" style="margin-left:.3rem" title="Eliminar">🗑️</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

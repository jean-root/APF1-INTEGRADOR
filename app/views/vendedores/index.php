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
      navigator.clipboard.writeText(el.textContent || '').then(() => { alert('Contraseña copiada'); });
    }
  </script>
  <?php unset($_SESSION['temp_password'], $_SESSION['temp_user_email']); ?>
<?php endif; ?>

<div class="page-header">
  <div>
    <h2>👥 Gestión de Vendedores</h2>
    <p><?= count($vendedores) ?> vendedor<?= count($vendedores) !== 1 ? 'es' : '' ?> registrado<?= count($vendedores) !== 1 ? 's' : '' ?></p>
  </div>
  <a href="<?= BASE_URL ?>/vendedor/crear" class="btn btn-primary">+ Nuevo Vendedor</a>
</div>

<div class="admin-card">
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Zona</th><th>Comisión</th><th>Acceso</th><th>Registrado</th><th>Acciones</th></tr>
    </thead>
    <tbody>
      <?php if (empty($vendedores)): ?>
        <tr>
          <td colspan="9" style="text-align:center;padding:3rem;color:var(--text-3)">
            No hay vendedores. <a href="<?= BASE_URL ?>/vendedor/crear" style="color:var(--accent)">Agregar primero →</a>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($vendedores as $v): ?>
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">#<?= $v->id ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:.7rem">
              <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--primary));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem;flex-shrink:0">
                <?= strtoupper(substr($v->nombre, 0, 1)) ?>
              </div>
              <strong><?= htmlspecialchars($v->nombre . ' ' . $v->apellido) ?></strong>
            </div>
          </td>
          <td style="font-size:.87rem"><?= htmlspecialchars($v->email) ?></td>
          <td style="font-size:.87rem"><?= htmlspecialchars($v->telefono ?: '—') ?></td>
          <td style="font-size:.87rem"><?= htmlspecialchars($v->zona ?? '') ?: '—' ?></td>
          <td style="font-size:.87rem"><?= isset($v->comision) ? number_format((float)$v->comision, 2) . '%' : '—' ?></td>
          <td>
            <?php if (!empty($v->usuario_id)): ?>
              <span class="badge badge-green">Con acceso</span>
            <?php else: ?>
              <a href="<?= BASE_URL ?>/vendedor/otorgarAcceso/<?= $v->id ?>" class="badge badge-gold" style="text-decoration:none" title="Crear login para este vendedor">Otorgar acceso</a>
            <?php endif; ?>
          </td>
          <td style="font-size:.8rem"><?= date('d/m/Y', strtotime($v->created_at)) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/vendedor/editar/<?= $v->id ?>" class="btn btn-sm btn-dark" title="Editar">✏️</a>
            <a href="<?= BASE_URL ?>/vendedor/reasignar/<?= $v->id ?>" class="btn btn-sm btn-dark" style="margin-left:.3rem" title="Reasignar propiedades">🔄</a>
            <a href="<?= BASE_URL ?>/vendedor/eliminar/<?= $v->id ?>" class="btn btn-sm btn-danger btn-delete" style="margin-left:.3rem" title="Eliminar">🗑️</a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

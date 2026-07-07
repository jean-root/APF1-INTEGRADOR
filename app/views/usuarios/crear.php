<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:760px">
  <h2>Nuevo Usuario</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-error">
      <?= implode('<br>', array_map('htmlspecialchars', $errores)) ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <?= $this->csrfField() ?>
    <div class="form-group">
      <label>Nombre</label>
      <input type="text" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Contraseña temporal</label>
      <div style="font-size:.85rem;color:var(--text-3)">Se genera automaticamente al guardar.</div>
    </div>

    <div class="form-group">
      <label>Rol</label>
      <select name="rol" required>
        <option value="supervisor" selected>Supervisor</option>
        <option value="admin">Admin (TI)</option>
      </select>
    </div>

    <div class="form-group" style="display:flex;align-items:center;gap:.6rem">
      <input type="checkbox" name="estado" id="estado" checked>
      <label for="estado" style="margin:0">Activo</label>
    </div>

    <div class="form-group" style="display:flex;align-items:center;gap:.6rem">
      <input type="checkbox" name="password_reset_required" id="password_reset_required" checked>
      <label for="password_reset_required" style="margin:0">Forzar cambio de contraseña al primer ingreso</label>
    </div>

    <div style="display:flex;gap:.6rem;margin-top:1rem">
      <button type="submit" class="btn btn-primary">Guardar</button>
      <a href="<?= BASE_URL ?>/usuario" class="btn btn-dark">Cancelar</a>
    </div>
  </form>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

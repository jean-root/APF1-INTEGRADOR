<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="admin-card" style="max-width:760px">
  <h2>Editar Usuario</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-error">
      <?= implode('<br>', array_map('htmlspecialchars', $errores)) ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <?= $this->csrfField() ?>
    <div class="form-group">
      <label>Nombre</label>
      <input type="text" name="nombre" required value="<?= htmlspecialchars($_POST['nombre'] ?? $usuario->nombre) ?>">
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? $usuario->email) ?>">
    </div>

    <div class="form-group">
      <label>Nueva contraseña (opcional)</label>
      <input type="password" name="password" placeholder="Deja en blanco para mantener">
    </div>

    <div class="form-group">
      <label>Rol</label>
      <?php $rolActual = $_POST['rol'] ?? $usuario->rol; ?>
      <select name="rol" required>
        <option value="supervisor" <?= $rolActual === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
        <option value="admin" <?= $rolActual === 'admin' ? 'selected' : '' ?>>Admin (TI)</option>
      </select>
    </div>

    <?php $estadoActual = isset($_POST['estado']) ? 1 : (int)$usuario->estado; ?>
    <div class="form-group" style="display:flex;align-items:center;gap:.6rem">
      <input type="checkbox" name="estado" id="estado" <?= $estadoActual === 1 ? 'checked' : '' ?>>
      <label for="estado" style="margin:0">Activo</label>
    </div>

    <?php $resetActual = isset($_POST['password_reset_required']) ? 1 : (int)($usuario->password_reset_required ?? 1); ?>
    <div class="form-group" style="display:flex;align-items:center;gap:.6rem">
      <input type="checkbox" name="password_reset_required" id="password_reset_required" <?= $resetActual === 1 ? 'checked' : '' ?>>
      <label for="password_reset_required" style="margin:0">Forzar cambio de contraseña al primer ingreso</label>
    </div>

    <div style="display:flex;gap:.6rem;margin-top:1rem">
      <button type="submit" class="btn btn-primary">Guardar cambios</button>
      <a href="<?= BASE_URL ?>/usuario" class="btn btn-dark">Cancelar</a>
    </div>
  </form>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

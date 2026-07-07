<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div><h2>➕ Nuevo Vendedor</h2></div>
  <a href="<?= BASE_URL ?>/vendedor" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">← Volver</a>
</div>

<?php if (!empty($errores)): ?>
<div class="alert alert-error" style="max-width:600px;margin-bottom:1.5rem">
  <?php foreach ($errores as $e): ?><div>⚠️ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="form-card" style="max-width:600px">
  <form method="POST" data-validate>
    <?= $this->csrfField() ?>
    <div class="form-grid">
      <div class="form-group">
        <label>Nombre *</label>
        <input type="text" name="nombre" required placeholder="Gabriel"
               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Apellido *</label>
        <input type="text" name="apellido" required placeholder="Gamero"
               value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">
      </div>
      <div class="form-group form-full">
        <label>Email *</label>
        <input type="email" name="email" required placeholder="gabriel@hogarideal.pe"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="form-group form-full">
        <label>Teléfono</label>
        <input type="text" name="telefono" placeholder="+51 936 338 196"
               value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Zona asignada</label>
        <input type="text" name="zona" placeholder="Ej: Surco, Miraflores"
               value="<?= htmlspecialchars($_POST['zona'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Comisión (%)</label>
        <input type="number" name="comision" min="0" max="100" step="0.5" placeholder="3.00"
               value="<?= htmlspecialchars($_POST['comision'] ?? '3.00') ?>">
      </div>
      <div class="form-group form-full" style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem">
        <input type="checkbox" id="crear_acceso" name="crear_acceso" value="1" style="width:auto"
               <?= isset($_POST['crear_acceso']) ? 'checked' : '' ?>>
        <label for="crear_acceso" style="margin:0">Otorgar acceso al sistema (login propio para el vendedor)</label>
      </div>
    </div>
    <div style="margin-top:1.5rem;display:flex;gap:1rem">
      <button type="submit" class="btn btn-primary">💾 Guardar Vendedor</button>
      <a href="<?= BASE_URL ?>/vendedor" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">Cancelar</a>
    </div>
  </form>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

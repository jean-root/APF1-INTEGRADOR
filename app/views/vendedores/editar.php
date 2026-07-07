<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div><h2>✏️ Editar Vendedor</h2><p><?= htmlspecialchars($vendedor->nombre . ' ' . $vendedor->apellido) ?></p></div>
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
        <input type="text" name="nombre" required
               value="<?= htmlspecialchars($_POST['nombre'] ?? $vendedor->nombre) ?>">
      </div>
      <div class="form-group">
        <label>Apellido *</label>
        <input type="text" name="apellido" required
               value="<?= htmlspecialchars($_POST['apellido'] ?? $vendedor->apellido) ?>">
      </div>
      <div class="form-group form-full">
        <label>Email *</label>
        <input type="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? $vendedor->email) ?>">
      </div>
      <div class="form-group form-full">
        <label>Teléfono</label>
        <input type="text" name="telefono"
               value="<?= htmlspecialchars($_POST['telefono'] ?? $vendedor->telefono) ?>">
      </div>
      <div class="form-group">
        <label>Zona asignada</label>
        <input type="text" name="zona" placeholder="Ej: Surco, Miraflores"
               value="<?= htmlspecialchars($_POST['zona'] ?? $vendedor->zona ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Comisión (%)</label>
        <input type="number" name="comision" min="0" max="100" step="0.5"
               value="<?= htmlspecialchars($_POST['comision'] ?? $vendedor->comision ?? '3.00') ?>">
      </div>
    </div>
    <div style="margin-top:1.5rem;display:flex;gap:1rem">
      <button type="submit" class="btn btn-primary">💾 Actualizar</button>
      <a href="<?= BASE_URL ?>/vendedor/reasignar/<?= $vendedor->id ?>" class="btn btn-dark">🔄 Reasignar propiedades</a>
      <a href="<?= BASE_URL ?>/vendedor" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">Cancelar</a>
    </div>
  </form>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

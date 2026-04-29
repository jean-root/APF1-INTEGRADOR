<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>➕ Nueva Propiedad</h2>
    <p>Completa el formulario para publicar una propiedad.</p>
  </div>
  <a href="<?= BASE_URL ?>/propiedad/admin" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">← Volver</a>
</div>

<?php if (!empty($errores)): ?>
<div class="alert alert-error" style="max-width:800px;margin-bottom:1.5rem">
  <?php foreach ($errores as $e): ?><div>⚠️ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="form-card">
  <form method="POST" enctype="multipart/form-data" data-validate>

    <div class="form-grid">

      <!-- Título -->
      <div class="form-group form-full">
        <label>Título de la propiedad *</label>
        <input type="text" name="titulo" placeholder="Ej: Casa Moderna en Santiago de Surco" required
               value="<?= htmlspecialchars($_POST['titulo'] ?? '') ?>">
      </div>

      <!-- Tipo y Precio -->
      <div class="form-group">
        <label>Tipo de propiedad *</label>
        <select name="tipo" required>
          <?php foreach (['casa','departamento','terreno','local'] as $t): ?>
            <option value="<?= $t ?>" <?= (($_POST['tipo'] ?? '') === $t) ? 'selected' : '' ?>>
              <?= ucfirst($t) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Precio (S/) *</label>
        <input type="number" name="precio" placeholder="250000" min="0" step="0.01" required
               value="<?= htmlspecialchars($_POST['precio'] ?? '') ?>">
      </div>

      <!-- Características -->
      <div class="form-group">
        <label>Habitaciones</label>
        <input type="number" name="habitaciones" placeholder="0" min="0"
               value="<?= htmlspecialchars($_POST['habitaciones'] ?? '0') ?>">
      </div>
      <div class="form-group">
        <label>Baños</label>
        <input type="number" name="banos" placeholder="0" min="0"
               value="<?= htmlspecialchars($_POST['banos'] ?? '0') ?>">
      </div>
      <div class="form-group">
        <label>Estacionamientos</label>
        <input type="number" name="estacionamientos" placeholder="0" min="0"
               value="<?= htmlspecialchars($_POST['estacionamientos'] ?? '0') ?>">
      </div>
      <div class="form-group">
        <label>Metros cuadrados (m²)</label>
        <input type="number" name="metros2" placeholder="120" min="0" step="0.01"
               value="<?= htmlspecialchars($_POST['metros2'] ?? '') ?>">
      </div>

      <!-- Dirección y Vendedor -->
      <div class="form-group form-full">
        <label>Dirección</label>
        <input type="text" name="direccion" placeholder="Av. El Derby 278, Santiago de Surco, Lima"
               value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Vendedor responsable</label>
        <select name="vendedor_id">
          <option value="">— Sin asignar —</option>
          <?php foreach ($vendedores as $v): ?>
            <option value="<?= $v->id ?>" <?= (($_POST['vendedor_id'] ?? '') == $v->id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($v->nombre . ' ' . $v->apellido) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="display:flex;flex-direction:row;align-items:center;gap:.75rem;padding-top:1.5rem">
        <input type="checkbox" name="activo" id="activo" value="1"
               <?= empty($_POST) || isset($_POST['activo']) ? 'checked' : '' ?>
               style="width:auto;accent-color:var(--accent)">
        <label for="activo" style="margin:0;cursor:pointer">Propiedad activa (visible en catálogo)</label>
      </div>

      <!-- Descripción -->
      <div class="form-group form-full">
        <label>Descripción</label>
        <textarea name="descripcion" placeholder="Describe la propiedad con sus características principales..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
      </div>

      <!-- Imagen -->
      <div class="form-group form-full">
        <label>Imagen de la propiedad</label>
        <div class="upload-area">
          <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp">
          <div class="upload-icon">📷</div>
          <p class="upload-text">Haz clic o arrastra una imagen aquí<br><small style="color:var(--text-3)">JPG, PNG, WEBP – máx. 5 MB</small></p>
        </div>
        <img id="preview-img" alt="Vista previa">
      </div>

    </div><!-- /.form-grid -->

    <div style="display:flex;gap:1rem;margin-top:1.75rem">
      <button type="submit" class="btn btn-primary" style="padding:.8rem 2rem">
        💾 Guardar Propiedad
      </button>
      <a href="<?= BASE_URL ?>/propiedad/admin" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

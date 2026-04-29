<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>✏️ Editar Propiedad</h2>
    <p>Modifica los datos de: <strong><?= htmlspecialchars($propiedad->titulo) ?></strong></p>
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

      <div class="form-group form-full">
        <label>Título de la propiedad *</label>
        <input type="text" name="titulo" required
               value="<?= htmlspecialchars($_POST['titulo'] ?? $propiedad->titulo) ?>">
      </div>

      <div class="form-group">
        <label>Tipo de propiedad *</label>
        <select name="tipo" required>
          <?php foreach (['casa','departamento','terreno','local'] as $t): ?>
            <option value="<?= $t ?>"
              <?= (($_POST['tipo'] ?? $propiedad->tipo) === $t) ? 'selected' : '' ?>>
              <?= ucfirst($t) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Precio (S/) *</label>
        <input type="number" name="precio" min="0" step="0.01" required
               value="<?= htmlspecialchars($_POST['precio'] ?? $propiedad->precio) ?>">
      </div>

      <div class="form-group">
        <label>Habitaciones</label>
        <input type="number" name="habitaciones" min="0"
               value="<?= htmlspecialchars($_POST['habitaciones'] ?? $propiedad->habitaciones) ?>">
      </div>
      <div class="form-group">
        <label>Baños</label>
        <input type="number" name="banos" min="0"
               value="<?= htmlspecialchars($_POST['banos'] ?? $propiedad->banos) ?>">
      </div>
      <div class="form-group">
        <label>Estacionamientos</label>
        <input type="number" name="estacionamientos" min="0"
               value="<?= htmlspecialchars($_POST['estacionamientos'] ?? $propiedad->estacionamientos) ?>">
      </div>
      <div class="form-group">
        <label>Metros cuadrados (m²)</label>
        <input type="number" name="metros2" min="0" step="0.01"
               value="<?= htmlspecialchars($_POST['metros2'] ?? $propiedad->metros2) ?>">
      </div>

      <div class="form-group form-full">
        <label>Dirección</label>
        <input type="text" name="direccion"
               value="<?= htmlspecialchars($_POST['direccion'] ?? $propiedad->direccion) ?>">
      </div>

      <div class="form-group">
        <label>Vendedor responsable</label>
        <select name="vendedor_id">
          <option value="">— Sin asignar —</option>
          <?php foreach ($vendedores as $v): ?>
            <option value="<?= $v->id ?>"
              <?= (($_POST['vendedor_id'] ?? $propiedad->vendedor_id) == $v->id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($v->nombre . ' ' . $v->apellido) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="display:flex;flex-direction:row;align-items:center;gap:.75rem;padding-top:1.5rem">
        <input type="checkbox" name="activo" id="activo" value="1"
               <?= (($_POST['activo'] ?? $propiedad->activo) ? 'checked' : '') ?>
               style="width:auto;accent-color:var(--accent)">
        <label for="activo" style="margin:0;cursor:pointer">Propiedad activa (visible en catálogo)</label>
      </div>

      <div class="form-group form-full">
        <label>Descripción</label>
        <textarea name="descripcion"><?= htmlspecialchars($_POST['descripcion'] ?? $propiedad->descripcion) ?></textarea>
      </div>

      <!-- Imagen actual -->
      <div class="form-group form-full">
        <label>Imagen de la propiedad</label>
        <?php if ($propiedad->imagen !== 'no-imagen.jpg'): ?>
          <div style="margin-bottom:.75rem">
            <p style="font-size:.8rem;color:var(--text-3);margin-bottom:.4rem">Imagen actual:</p>
            <img src="<?= UPLOAD_URL . htmlspecialchars($propiedad->imagen) ?>"
                 style="max-height:160px;border-radius:var(--radius);object-fit:cover;width:100%"
                 onerror="this.style.display='none'" alt="Imagen actual">
          </div>
        <?php endif; ?>
        <div class="upload-area">
          <input type="file" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.webp">
          <div class="upload-icon">📷</div>
          <p class="upload-text">Subir nueva imagen (opcional)<br><small style="color:var(--text-3)">JPG, PNG, WEBP – máx. 5 MB</small></p>
        </div>
        <img id="preview-img" alt="Vista previa">
      </div>

    </div>

    <div style="display:flex;gap:1rem;margin-top:1.75rem">
      <button type="submit" class="btn btn-primary" style="padding:.8rem 2rem">
        💾 Actualizar Propiedad
      </button>
      <a href="<?= BASE_URL ?>/propiedad/admin" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">Cancelar</a>
    </div>

  </form>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

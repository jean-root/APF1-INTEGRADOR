<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>🔄 Reasignar Propiedades</h2>
    <p>Vendedor de origen: <strong><?= htmlspecialchars($origen->nombre . ' ' . $origen->apellido) ?></strong>
      (<?= count($propiedadesOrigen) ?> propiedad<?= count($propiedadesOrigen) !== 1 ? 'es' : '' ?> asignada<?= count($propiedadesOrigen) !== 1 ? 's' : '' ?>)</p>
  </div>
  <a href="<?= BASE_URL ?>/vendedor" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">← Volver</a>
</div>

<?php if (!empty($errores)): ?>
<div class="alert alert-error" style="max-width:700px;margin-bottom:1.5rem">
  <?php foreach ($errores as $e): ?><div>⚠️ <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($propiedadesOrigen)): ?>
  <div class="admin-card" style="max-width:700px;text-align:center;padding:3rem;color:var(--text-3)">
    Este vendedor no tiene propiedades asignadas actualmente.
  </div>
<?php else: ?>

<div class="admin-card" style="max-width:700px">
  <p style="margin-bottom:1rem;font-size:.9rem;color:var(--text-3)">
    Todas las propiedades listadas abajo pasarán al vendedor que selecciones.
  </p>

  <table class="data-table" style="margin-bottom:1.5rem">
    <thead><tr><th>Propiedad</th><th>Tipo</th><th>Precio</th></tr></thead>
    <tbody>
      <?php foreach ($propiedadesOrigen as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p->titulo) ?></td>
        <td><span class="badge badge-blue"><?= ucfirst($p->tipo) ?></span></td>
        <td><?= Propiedad::formatearPrecio((float)$p->precio) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <form method="POST">
    <?= $this->csrfField() ?>
    <div class="form-group">
      <label>Reasignar todas a:</label>
      <select name="vendedor_destino" required style="width:100%">
        <option value="">— Selecciona un vendedor —</option>
        <?php foreach ($otrosVendedores as $v): ?>
          <option value="<?= $v->id ?>"><?= htmlspecialchars($v->nombre . ' ' . $v->apellido) ?><?= $v->zona ? ' — ' . htmlspecialchars($v->zona) : '' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="margin-top:1.5rem;display:flex;gap:1rem">
      <button type="submit" class="btn btn-primary">🔄 Confirmar reasignación</button>
      <a href="<?= BASE_URL ?>/vendedor" class="btn btn-outline" style="border-color:var(--border);color:var(--text)">Cancelar</a>
    </div>
  </form>
</div>

<?php endif; ?>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

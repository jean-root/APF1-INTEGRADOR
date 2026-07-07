<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>🎯 Lead de <?= htmlspecialchars($lead->nombre) ?></h2>
    <p style="font-size:.85rem;color:var(--text-3)">Recibido el <?= date('d/m/Y H:i', strtotime($lead->created_at)) ?></p>
  </div>
  <a href="<?= BASE_URL ?>/panel" class="btn btn-dark">← Volver a mi panel</a>
</div>

<div class="admin-card" style="max-width:700px">
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3);width:140px">Nombre</td>
      <td style="padding:.5rem 0"><strong><?= htmlspecialchars($lead->nombre) ?></strong></td>
    </tr>
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3)">Email</td>
      <td style="padding:.5rem 0"><a href="mailto:<?= htmlspecialchars($lead->email) ?>"><?= htmlspecialchars($lead->email) ?></a></td>
    </tr>
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3)">Teléfono</td>
      <td style="padding:.5rem 0"><?= htmlspecialchars($lead->telefono ?: '—') ?></td>
    </tr>
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3)">Asunto</td>
      <td style="padding:.5rem 0"><?= htmlspecialchars($lead->asunto ?: '—') ?></td>
    </tr>
  </table>

  <hr style="margin:1.2rem 0;border-color:var(--border)">

  <p style="white-space:pre-wrap;line-height:1.6"><?= htmlspecialchars($lead->mensaje) ?></p>
</div>

<!-- Actualizar estado del pipeline -->
<div class="admin-card" style="max-width:700px;margin-top:1.5rem">
  <div class="admin-card__header"><span class="admin-card__title">🔄 Actualizar estado</span></div>

  <form method="POST" action="<?= BASE_URL ?>/panel/cambiarEstado/<?= $lead->id ?>" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;margin-top:.75rem">
    <?= $this->csrfField() ?>
    <select name="estado" style="max-width:240px">
      <?php foreach (Mensaje::estadosValidos() as $estado): ?>
        <option value="<?= $estado ?>" <?= ($lead->estado ?? 'nuevo') === $estado ? 'selected' : '' ?>>
          <?= htmlspecialchars(Mensaje::etiquetaEstado($estado)) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-sm btn-primary">Guardar estado</button>
  </form>
  <p style="font-size:.8rem;color:var(--text-3);margin-top:.75rem">
    Cada cambio queda registrado con fecha y hora en el historial de abajo — es la trazabilidad de tu actividad comercial sobre este lead.
  </p>
</div>

<!-- Historial -->
<div class="admin-card" style="max-width:700px;margin-top:1.5rem">
  <div class="admin-card__header"><span class="admin-card__title">📋 Historial de este lead</span></div>
  <?php if (empty($historial)): ?>
    <p style="color:var(--text-3);padding:.5rem 0">Todavía no hay actividad registrada.</p>
  <?php else: ?>
    <ul style="list-style:none;padding:0;margin:.5rem 0 0;border-left:2px solid var(--border)">
      <?php foreach ($historial as $h): ?>
        <li style="padding:.4rem 0 .4rem 1rem;font-size:.85rem;position:relative">
          <span style="position:absolute;left:-6px;top:.6rem;width:10px;height:10px;border-radius:50%;background:var(--accent)"></span>
          <span style="color:var(--text-3)"><?= date('d/m/Y H:i', strtotime($h->created_at)) ?></span> —
          <strong><?= htmlspecialchars($h->usuario_nombre) ?></strong>: <?= htmlspecialchars($h->detalle ?? '') ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<?php require_once APP_ROOT . '/app/views/layouts/admin_footer.php'; ?>

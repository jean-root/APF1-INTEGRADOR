<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>✉️ Mensaje de <?= htmlspecialchars($mensaje->nombre) ?></h2>
    <p style="font-size:.85rem;color:var(--text-3)">Recibido el <?= date('d/m/Y H:i', strtotime($mensaje->created_at)) ?></p>
  </div>
  <a href="<?= BASE_URL ?>/mensaje" class="btn btn-dark">← Volver a la bandeja</a>
</div>

<div class="admin-card" style="max-width:700px">
  <table style="width:100%;border-collapse:collapse">
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3);width:140px">Nombre</td>
      <td style="padding:.5rem 0"><strong><?= htmlspecialchars($mensaje->nombre) ?></strong></td>
    </tr>
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3)">Email</td>
      <td style="padding:.5rem 0"><a href="mailto:<?= htmlspecialchars($mensaje->email) ?>"><?= htmlspecialchars($mensaje->email) ?></a></td>
    </tr>
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3)">Teléfono</td>
      <td style="padding:.5rem 0"><?= htmlspecialchars($mensaje->telefono ?: '—') ?></td>
    </tr>
    <tr>
      <td style="padding:.5rem 0;color:var(--text-3)">Asunto</td>
      <td style="padding:.5rem 0"><?= htmlspecialchars($mensaje->asunto ?: '—') ?></td>
    </tr>
  </table>

  <hr style="margin:1.2rem 0;border-color:var(--border)">

  <p style="white-space:pre-wrap;line-height:1.6"><?= htmlspecialchars($mensaje->mensaje) ?></p>
</div>

<!-- Pipeline de ventas (HU-28): asignación a vendedor y estado actual -->
<div class="admin-card" style="max-width:700px;margin-top:1.5rem">
  <div class="admin-card__header"><span class="admin-card__title">🎯 Seguimiento comercial</span></div>

  <p style="margin:.5rem 0 1rem">
    Estado actual:
    <span class="badge <?= match($mensaje->estado ?? 'nuevo') {
        'nuevo' => 'badge-gold', 'contactado', 'visita_agendada' => 'badge-blue',
        'cerrado' => 'badge-green', 'perdido' => 'badge-danger', default => 'badge-gray' } ?>">
      <?= htmlspecialchars(Mensaje::etiquetaEstado($mensaje->estado ?? 'nuevo')) ?>
    </span>
    <?php if ($vendedorAsignado): ?>
      — asignado a <strong><?= htmlspecialchars($vendedorAsignado->nombre . ' ' . $vendedorAsignado->apellido) ?></strong>
    <?php endif; ?>
  </p>

  <form method="POST" action="<?= BASE_URL ?>/mensaje/asignar/<?= $mensaje->id ?>" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap">
    <?= $this->csrfField() ?>
    <select name="vendedor_id" style="max-width:260px">
      <option value="">— Seleccionar vendedor —</option>
      <?php foreach ($vendedores as $v): ?>
        <option value="<?= $v->id ?>" <?= ($vendedorAsignado && (int)$vendedorAsignado->id === (int)$v->id) ? 'selected' : '' ?>>
          <?= htmlspecialchars($v->nombre . ' ' . $v->apellido) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-sm btn-primary"><?= $vendedorAsignado ? 'Reasignar' : 'Asignar' ?></button>
  </form>

  <p style="font-size:.8rem;color:var(--text-3);margin-top:.75rem">
    El vendedor asignado actualiza el estado (contactado, visita agendada, cerrado o perdido) desde su propio panel.
  </p>
</div>

<!-- Trazabilidad de actividad (bitácora del lead) -->
<div class="admin-card" style="max-width:700px;margin-top:1.5rem">
  <div class="admin-card__header"><span class="admin-card__title">📋 Historial de actividad</span></div>
  <?php if (empty($historial)): ?>
    <p style="color:var(--text-3);padding:.5rem 0">Sin actividad registrada todavía sobre este lead.</p>
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

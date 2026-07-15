<?php require_once APP_ROOT . '/app/views/layouts/admin_header.php'; ?>

<div class="page-header">
  <div>
    <h2>🗂️ Bitácora de Auditoría</h2>
    <p>Registro de actividad del sistema, visto desde dos niveles complementarios.</p>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card__header">
    <span class="admin-card__title">1. Bitácora de aplicación (quién hizo qué)</span>
  </div>
  <p style="padding:0 0 .75rem;color:var(--text-3);font-size:.85rem">
    Generada por el propio sistema PHP (modelo <code>LogAccion</code>): identifica al usuario de la sesión que ejecutó la acción. Últimos <?= count($registros) ?> registro<?= count($registros) !== 1 ? 's' : '' ?>.
  </p>
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Entidad</th><th>Detalle</th><th>IP</th></tr>
    </thead>
    <tbody>
      <?php if (empty($registros)): ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-3)">
            Sin actividad registrada todavía.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($registros as $r): ?>
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">#<?= $r->id ?></td>
          <td style="font-size:.8rem"><?= date('d/m/Y H:i', strtotime($r->created_at)) ?></td>
          <td><strong><?= htmlspecialchars($r->usuario_nombre) ?></strong></td>
          <td>
            <?php
              $badge = match ($r->accion) {
                  'crear'         => 'badge-green',
                  'editar'        => 'badge-blue',
                  'eliminar'      => 'badge-danger',
                  'enviar_correo' => 'badge-gold',
                  default         => 'badge-gray',
              };
              $etiquetaAccion = $r->accion === 'enviar_correo' ? 'Correo enviado' : ucfirst($r->accion);
            ?>
            <span class="badge <?= $badge ?>"><?= $etiquetaAccion ?></span>
          </td>
          <td style="font-size:.87rem"><?= htmlspecialchars(ucfirst($r->entidad)) ?><?= $r->entidad_id ? ' #' . $r->entidad_id : '' ?></td>
          <td style="font-size:.85rem;color:var(--text-3)"><?= htmlspecialchars($r->detalle ?? '—') ?></td>
          <td style="font-size:.78rem;color:var(--text-3)"><?= htmlspecialchars($r->ip ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Auditoría a nivel de base de datos, generada automáticamente por triggers -->
<div class="admin-card" style="margin-top:1.5rem">
  <div class="admin-card__header">
    <span class="admin-card__title">2. Auditoría de base de datos (trigger MySQL)</span>
  </div>
  <p style="padding:0 0 .75rem;color:var(--text-3);font-size:.85rem">
    Generada automáticamente por triggers <code>AFTER INSERT/UPDATE/DELETE</code> en <code>propiedades</code>, <code>vendedores</code> y <code>usuarios</code> (migración <code>005_auditoria_trigger.sql</code>). Registra el cambio a nivel de motor de BD, incluso si ocurriera fuera de la aplicación. Últimos <?= count($registrosBd) ?> registro<?= count($registrosBd) !== 1 ? 's' : '' ?>.
  </p>
  <table class="data-table">
    <thead>
      <tr><th>#</th><th>Fecha</th><th>Tabla</th><th>Operación</th><th>Registro</th><th>Datos anteriores</th><th>Datos nuevos</th></tr>
    </thead>
    <tbody>
      <?php if (empty($registrosBd)): ?>
        <tr>
          <td colspan="7" style="text-align:center;padding:2rem;color:var(--text-3)">
            Sin registros todavía, o falta correr la migración <code>005_auditoria_trigger.sql</code> en esta base de datos.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($registrosBd as $a): ?>
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">#<?= $a->id ?></td>
          <td style="font-size:.8rem"><?= date('d/m/Y H:i', strtotime($a->fecha_hora)) ?></td>
          <td style="font-size:.87rem"><strong><?= htmlspecialchars($a->tabla_afectada) ?></strong></td>
          <td>
            <?php
              $badgeOp = match ($a->operacion) {
                  'INSERT' => 'badge-green',
                  'UPDATE' => 'badge-blue',
                  'DELETE' => 'badge-danger',
                  default  => 'badge-gray',
              };
            ?>
            <span class="badge <?= $badgeOp ?>"><?= $a->operacion ?></span>
          </td>
          <td style="font-size:.8rem;color:var(--text-3)">#<?= $a->registro_id ?></td>
          <td style="font-size:.75rem;color:var(--text-3);max-width:220px;overflow-wrap:anywhere"><?= htmlspecialchars($a->datos_anteriores ?? '—') ?></td>
          <td style="font-size:.75rem;color:var(--text-3);max-width:220px;overflow-wrap:anywhere"><?= htmlspecialchars($a->datos_nuevos ?? '—') ?></t
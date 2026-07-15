<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($titulo) ?> – <?= APP_NAME ?></title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; color: #111; padding: 2rem; }
    .reporte-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #FACC15; padding-bottom: 1rem; margin-bottom: 1.5rem; }
    .reporte-header h1 { font-size: 1.4rem; margin: 0; }
    .reporte-header p { margin: .2rem 0 0; color: #666; font-size: .85rem; }
    table { width: 100%; border-collapse: collapse; font-size: .82rem; }
    th, td { border: 1px solid #ddd; padding: .5rem .6rem; text-align: left; }
    th { background: #111; color: #fff; }
    tr:nth-child(even) { background: #f7f7f7; }
    .no-print { margin-bottom: 1.5rem; }
    .no-print button {
      background: #111; color: #fff; border: none; padding: .7rem 1.4rem;
      border-radius: 8px; font-weight: 600; cursor: pointer; font-size: .9rem;
    }
    .no-print button:hover { background: #FACC15; color: #111; }
    @media print {
      .no-print { display: none; }
      body { padding: 0; }
    }
  </style>
</head>
<body>

  <div class="no-print">
    <button onclick="window.print()">🖨️ Guardar como PDF / Imprimir</button>
  </div>

  <div class="reporte-header">
    <div>
      <h1>🏠 <?= htmlspecialchars(APP_NAME) ?></h1>
      <p><?= htmlspecialchars($titulo) ?> — generado el <?= date('d/m/Y H:i') ?></p>
    </div>
    <p><?= count($filas) ?> registro<?= count($filas) !== 1 ? 's' : '' ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <?php foreach ($columnas as $col): ?>
          <th><?= htmlspecialchars($col) ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($filas)): ?>
        <tr><td colspan="<?= count($columnas) ?>" style="text-align:center;padding:2rem;color:#888">Sin datos para este reporte.</td></tr>
      <?php else: ?>
        <?php foreach ($filas as $fila): ?>
          <tr>
            <?php foreach ($fila as $valor): ?>
              <td><?= htmlspecialchars((string) $valor) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>

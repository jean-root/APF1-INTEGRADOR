<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cambiar Contraseña – <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
</head>
<body>

<div class="login-page">
  <div class="login-left">
    <div class="login-card">
      <div class="login-card__logo">🏠 <span>Hogar Ideal</span> Perú</div>
      <p class="login-card__sub">Seguridad de la cuenta</p>

      <h2>Cambiar Contraseña</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (!empty($exito)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($exito) ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= $this->csrfField() ?>
        <div class="form-group" style="margin-bottom:1rem">
          <label>Contraseña actual</label>
          <input type="password" name="actual" required>
        </div>

        <div class="form-group" style="margin-bottom:1rem">
          <label>Nueva contraseña</label>
          <input type="password" name="nueva" required placeholder="Mínimo 6 caracteres">
        </div>

        <div class="form-group" style="margin-bottom:1.5rem">
          <label>Confirmar nueva contraseña</label>
          <input type="password" name="confirmar" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;padding:.85rem">
          Guardar cambios →
        </button>
      </form>
    </div>
  </div>
</div>

<script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>

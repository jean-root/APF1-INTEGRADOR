<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión – <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
</head>
<body>

<div class="login-page">

  <!-- Formulario -->
  <div class="login-left">
    <div class="login-card">
      <div class="login-card__logo">🏠 <span>Hogar Ideal</span> Perú</div>
      <p class="login-card__sub">Panel Administrativo</p>

      <h2>Iniciar Sesión</h2>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" data-validate>

        <div class="form-group" style="margin-bottom:1rem">
          <label>Correo electrónico</label>
          <input type="email" name="email" placeholder="admin@hogarideal.pe" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group" style="margin-bottom:1.5rem">
          <label>Contraseña</label>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;padding:.85rem">
          Ingresar al Panel →
        </button>
      </form>

      <p style="text-align:center;margin-top:1.5rem;font-size:.82rem;color:#888">
        <a href="<?= BASE_URL ?>/" style="color:var(--primary)">← Volver al portal</a>
      </p>


    </div>
  </div>



</div>

<script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>

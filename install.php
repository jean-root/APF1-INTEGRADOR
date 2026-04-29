<?php
// ============================================================
//  INSTALL.PHP – Script de instalación del administrador
//  Visita: http://localhost/APF1-INTEGRADOR/install
//  ⚠️  ELIMINA ESTE ARCHIVO después de usarlo
// ============================================================

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    try {
        $db  = Database::getInstance();
        $db->query(
            "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)",
            [$nombre, $email, $password]
        );
        $message = '✅ Administrador creado correctamente. <strong>Elimina este archivo (install.php) ahora.</strong>';
        $success = true;
    } catch (Exception $e) {
        $message = '❌ Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Instalación – <?= APP_NAME ?></title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
        .card { background:#fff; border-radius:12px; padding:2.5rem; width:380px; box-shadow: 0 8px 32px rgba(0,0,0,.12); }
        h2 { color:#111111; margin-bottom:.5rem; }
        p.sub { color:#888; font-size:.85rem; margin-top:0; margin-bottom:1.5rem; }
        label { display:block; font-size:.85rem; color:#555; margin-bottom:.3rem; font-weight:600; }
        input { width:100%; padding:.7rem 1rem; border:1px solid #ddd; border-radius:8px; font-size:.95rem; box-sizing:border-box; margin-bottom:1rem; }
        button { width:100%; padding:.85rem; background:#111111; color:#fff; border:none; border-radius:8px; font-size:1rem; cursor:pointer; font-weight:600; }
        button:hover { background:#FACC15; }
        .msg { padding:1rem; border-radius:8px; margin-bottom:1rem; font-size:.9rem; }
        .msg.ok { background:#e6f4ea; color:#2e7d32; }
        .msg.err { background:#fce8e6; color:#c62828; }
        .warn { background:#fff3cd; border-left:4px solid #ffc107; padding:.8rem 1rem; border-radius:6px; font-size:.82rem; color:#856404; margin-bottom:1.2rem; }
    </style>
</head>
<body>
<div class="card">
    <h2>🔧 Configuración Inicial</h2>
    <p class="sub">Crea el usuario administrador del sistema.</p>
    <div class="warn">⚠️ Elimina este archivo después de crear el admin.</div>

    <?php if ($message): ?>
        <div class="msg <?= $success ? 'ok' : 'err' ?>"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST">
        <label>Nombre</label>
        <input type="text" name="nombre" value="Administrador" required>
        <label>Email</label>
        <input type="email" name="email" value="admin@hogarideal.pe" required>
        <label>Contraseña</label>
        <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
        <button type="submit" name="crear">Crear Administrador</button>
    </form>
    <?php else: ?>
        <a href="<?= BASE_URL ?>/auth/login" style="display:block;text-align:center;margin-top:1rem;color:#111111;font-weight:600;">Ir al Login →</a>
    <?php endif; ?>
</div>
</body>
</html>

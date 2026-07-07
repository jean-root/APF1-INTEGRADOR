<?php
// ============================================================
//  CONTROLLER: Auth – Login y Logout
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Usuario.php';

class AuthController extends Controller {

    private Usuario $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    // GET/POST /auth/login
    public function login(): void {
        Middleware::guest();

        $error = '';

        if ($this->isPost()) {
            $this->requireCsrf();
            $email    = $this->sanitize($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!$email || !$password) {
                $error = 'Completa todos los campos.';
            } else {
                $user = $this->usuario->findByEmail($email);

                if ($user && $this->usuario->verifyPassword($password, $user->password)) {
                    if ((int)($user->estado ?? 1) !== 1) {
                        $error = 'Tu cuenta está inactiva. Contacta al administrador.';
                    } else {
                        // Login exitoso
                        session_regenerate_id(true);
                        $_SESSION['usuario_id']     = $user->id;
                        $_SESSION['usuario_nombre'] = $user->nombre;
                        $_SESSION['usuario_email']  = $user->email;
                        $_SESSION['usuario_rol']    = $user->rol ?? 'supervisor';

                        if ((int)($user->password_reset_required ?? 0) === 1) {
                            $this->redirect('auth/cambiar');
                        }

                        $this->redirect($this->destinoPorRol($_SESSION['usuario_rol']));
                    }
                } else {
                    // Registrar intento fallido (R6)
                    Middleware::logFailedLogin($email);
                    $error = 'Credenciales incorrectas. Verifica tu email y contraseña.';
                }
            }
        }

        $this->render('auth/login', ['error' => $error]);
    }

    // GET /auth/logout
    public function logout(): void {
        session_destroy();
        $this->redirect('auth/login');
    }

    // GET/POST /auth/cambiar
    public function cambiar(): void {
        Middleware::auth();

        $error = '';
        $exito = '';

        if ($this->isPost()) {
            $this->requireCsrf();
            $actual   = trim($_POST['actual'] ?? '');
            $nueva    = trim($_POST['nueva'] ?? '');
            $confirm  = trim($_POST['confirmar'] ?? '');

            if (!$actual || !$nueva || !$confirm) {
                $error = 'Completa todos los campos.';
            } elseif (strlen($nueva) < 6) {
                $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
            } elseif ($nueva !== $confirm) {
                $error = 'La confirmación no coincide.';
            } else {
                $user = $this->usuario->findById((int)($_SESSION['usuario_id'] ?? 0));
                if (!$user || !$this->usuario->verifyPassword($actual, $user->password)) {
                    $error = 'La contraseña actual no es correcta.';
                } else {
                    $this->usuario->update((int)$user->id, [
                        'password' => password_hash($nueva, PASSWORD_BCRYPT),
                        'password_reset_required' => 0,
                    ]);
                    $exito = 'Contraseña actualizada correctamente.';
                    $this->redirect($this->destinoPorRol($_SESSION['usuario_rol'] ?? 'supervisor'));
                }
            }
        }

        $this->render('auth/cambiar', [
            'error' => $error,
            'exito' => $exito,
        ]);
    }

    // Destino de redirección tras login/cambio de clave, según rol (HU-27: rol vendedor -> /panel)
    private function destinoPorRol(string $rol): string {
        return match ($rol) {
            'admin'    => 'usuario',
            'vendedor' => 'panel',
            default    => 'admin/dashboard',
        };
    }
}

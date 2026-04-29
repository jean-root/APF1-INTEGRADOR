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
            $email    = $this->sanitize($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!$email || !$password) {
                $error = 'Completa todos los campos.';
            } else {
                $user = $this->usuario->findByEmail($email);

                if ($user && $this->usuario->verifyPassword($password, $user->password)) {
                    // Login exitoso
                    session_regenerate_id(true);
                    $_SESSION['usuario_id']     = $user->id;
                    $_SESSION['usuario_nombre'] = $user->nombre;
                    $_SESSION['usuario_email']  = $user->email;
                    $this->redirect('admin/dashboard');
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
}

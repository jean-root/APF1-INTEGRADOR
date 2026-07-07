<?php
// ============================================================
//  CONTROLLER: Usuario – Gestión de usuarios del sistema
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Usuario.php';
require_once APP_ROOT . '/app/models/LogAccion.php';

class UsuarioController extends Controller {

    private Usuario $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
    }

    // GET /usuario
    // Nota: las cuentas rol=vendedor no se listan aquí — se crean y gestionan
    // desde /vendedor (VendedorController), vinculadas 1:1 a un registro de
    // `vendedores`. Esta pantalla es solo para cuentas admin/supervisor.
    public function index(): void {
        Middleware::requireRole(['admin']);
        $usuarios = array_filter(
            $this->usuario->findAll('created_at DESC'),
            fn($u) => ($u->rol ?? '') !== 'vendedor'
        );
        $this->render('usuarios/index', [
            'titulo'   => 'Gestión de Usuarios',
            'usuarios' => $usuarios,
        ]);
    }

    // GET/POST /usuario/crear
    public function crear(): void {
        Middleware::requireRole(['admin']);
        $errores = [];

        if ($this->isPost()) {
            $this->requireCsrf();
            $datos = $this->recogerDatos($_POST);
            $errores = $this->validar($datos, false);

            if (empty($errores)) {
                $passwordPlano = $this->generarPasswordTemporal();
                $datos['password'] = password_hash($passwordPlano, PASSWORD_BCRYPT);
                $datos['password_reset_required'] = 1;
                try {
                    $nuevoId = $this->usuario->insert($datos);
                    LogAccion::registrar('crear', 'usuario', (int)$nuevoId, $datos['nombre'] . ' (' . $datos['rol'] . ')');
                    $this->flash('success', 'Usuario creado correctamente.');
                    $_SESSION['temp_password'] = $passwordPlano;
                    $_SESSION['temp_user_email'] = $datos['email'];
                    $this->redirect('usuario');
                } catch (Exception $e) {
                    $errores[] = 'No se pudo guardar el usuario. Verifica que el email no exista.';
                }
            }
        }

        $this->render('usuarios/crear', [
            'titulo'  => 'Nuevo Usuario',
            'errores' => $errores,
        ]);
    }

    // GET/POST /usuario/editar/{id}
    public function editar(string $id = '0'): void {
        Middleware::requireRole(['admin']);
        $usuario = $this->usuario->findById((int)$id);
        if (!$usuario) $this->redirect('usuario');
        if (($usuario->rol ?? '') === 'vendedor') {
            $this->flash('error', 'Las cuentas de vendedor se gestionan desde Vendedores.');
            $this->redirect('vendedor');
        }

        $errores = [];

        if ($this->isPost()) {
            $this->requireCsrf();
            $datos = $this->recogerDatos($_POST, false);
            $errores = $this->validar($datos, false);

            if (empty($errores)) {
                $update = [
                    'nombre' => $datos['nombre'],
                    'email'  => $datos['email'],
                    'rol'    => $datos['rol'],
                    'estado' => $datos['estado'],
                    'password_reset_required' => $datos['password_reset_required'],
                ];

                if (!empty($datos['password'])) {
                    $update['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
                }

                try {
                    $this->usuario->update((int)$id, $update);
                    LogAccion::registrar('editar', 'usuario', (int)$id, $update['nombre'] . ' (' . $update['rol'] . ')');
                    $this->flash('success', 'Usuario actualizado correctamente.');
                    $this->redirect('usuario');
                } catch (Exception $e) {
                    $errores[] = 'No se pudo actualizar el usuario. Verifica el email.';
                }
            }
        }

        $this->render('usuarios/editar', [
            'titulo'  => 'Editar Usuario',
            'usuario' => $usuario,
            'errores' => $errores,
        ]);
    }

    // GET /usuario/eliminar/{id}
    public function eliminar(string $id = '0'): void {
        Middleware::requireRole(['admin']);
        $usuario = $this->usuario->findById((int)$id);
        if ($usuario && ($usuario->rol ?? '') === 'vendedor') {
            $this->flash('error', 'Las cuentas de vendedor se gestionan desde Vendedores.');
            $this->redirect('vendedor');
        }
        $this->usuario->delete((int)$id);
        LogAccion::registrar('eliminar', 'usuario', (int)$id, $usuario->nombre ?? '');
        $this->flash('success', 'Usuario eliminado.');
        $this->redirect('usuario');
    }

    // GET /usuario/reset/{id}
    public function reset(string $id = '0'): void {
        Middleware::requireRole(['admin']);
        $usuario = $this->usuario->findById((int)$id);
        if (!$usuario || ($usuario->rol ?? '') === 'admin') {
            $this->redirect('usuario');
        }

        $passwordPlano = $this->generarPasswordTemporal();
        $this->usuario->update((int)$id, [
            'password' => password_hash($passwordPlano, PASSWORD_BCRYPT),
            'password_reset_required' => 1,
        ]);

        $_SESSION['temp_password'] = $passwordPlano;
        $_SESSION['temp_user_email'] = $usuario->email;
        LogAccion::registrar('editar', 'usuario', (int)$id, 'Contraseña reiniciada para ' . $usuario->nombre);
        $this->flash('success', 'Contraseña temporal generada.');
        $this->redirect('usuario');
    }

    private function recogerDatos(array $input, bool $requirePassword = true): array {
        return [
            'nombre'   => $this->sanitize($input['nombre'] ?? ''),
            'email'    => $this->sanitize($input['email'] ?? ''),
            'password' => trim($input['password'] ?? ''),
            'rol'      => $this->sanitize($input['rol'] ?? 'supervisor'),
            'estado'   => isset($input['estado']) ? 1 : 0,
            'password_reset_required' => isset($input['password_reset_required']) ? 1 : 0,
        ];
    }

    private function validar(array $datos, bool $requirePassword): array {
        $errores = [];
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido.';
        }
        if (!in_array($datos['rol'], ['admin', 'supervisor'], true)) {
            $errores[] = 'El rol no es válido.';
        }
        if ($requirePassword && strlen($datos['password']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        return $errores;
    }

    private function generarPasswordTemporal(int $length = 10): string {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $password = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }
        return $password;
    }
}

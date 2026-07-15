<?php
// ============================================================
//  CONTROLLER: Vendedor – CRUD
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Vendedor.php';
require_once APP_ROOT . '/app/models/Propiedad.php';
require_once APP_ROOT . '/app/models/Usuario.php';
require_once APP_ROOT . '/app/models/LogAccion.php';

class VendedorController extends Controller {

    private Vendedor  $vendedor;
    private Propiedad $propiedad;
    private Usuario   $usuario;

    public function __construct() {
        $this->vendedor  = new Vendedor();
        $this->propiedad = new Propiedad();
        $this->usuario   = new Usuario();
    }

    // GET /vendedor
    public function index(): void {
        Middleware::requireRole(['supervisor']);
        $vendedores = $this->vendedor->findAll('nombre ASC');

        // Adjuntar el estado de la cuenta de acceso (activo/inactivo) de cada vendedor con login
        foreach ($vendedores as $v) {
            $v->acceso_activo = null;
            if (!empty($v->usuario_id)) {
                $cuenta = $this->usuario->findById((int)$v->usuario_id);
                $v->acceso_activo = $cuenta ? (int)($cuenta->estado ?? 1) === 1 : null;
            }
        }

        $this->render('vendedores/index', [
            'titulo'     => 'Gestión de Vendedores',
            'vendedores' => $vendedores,
        ]);
    }

    // GET/POST /vendedor/crear
    public function crear(): void {
        Middleware::requireRole(['supervisor']);
        $errores = [];

        if ($this->isPost()) {
            $this->requireCsrf();
            $datos = [
                'nombre'   => $this->sanitize($_POST['nombre']   ?? ''),
                'apellido' => $this->sanitize($_POST['apellido'] ?? ''),
                'email'    => $this->sanitize($_POST['email']    ?? ''),
                'telefono' => $this->sanitize($_POST['telefono'] ?? ''),
                'zona'     => $this->sanitize($_POST['zona']     ?? ''),
                'comision' => (float) ($_POST['comision']        ?? 3.00),
            ];

            if (empty($datos['nombre']))   $errores[] = 'El nombre es obligatorio.';
            if (empty($datos['apellido'])) $errores[] = 'El apellido es obligatorio.';
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El email no es válido.';
            }
            if ($datos['comision'] < 0 || $datos['comision'] > 100) {
                $errores[] = 'La comisión debe estar entre 0 y 100.';
            }

            if (empty($errores)) {
                $nuevoId = $this->vendedor->insert($datos);
                LogAccion::registrar('crear', 'vendedor', (int)$nuevoId, $datos['nombre'] . ' ' . $datos['apellido']);

                // Otorgar acceso al sistema (HU-27: login propio del vendedor)
                if (!empty($_POST['crear_acceso'])) {
                    $this->crearAccesoParaVendedor((int)$nuevoId, $datos['nombre'] . ' ' . $datos['apellido'], $datos['email']);
                }

                $this->flash('success', 'Vendedor registrado correctamente.');
                $this->redirect('vendedor');
            }
        }

        $this->render('vendedores/crear', [
            'titulo'  => 'Nuevo Vendedor',
            'errores' => $errores,
        ]);
    }

    // GET /vendedor/otorgarAcceso/{id} (HU-27: dar login a un vendedor ya existente)
    public function otorgarAcceso(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $vendedor = $this->vendedor->findById((int)$id);
        if (!$vendedor) $this->redirect('vendedor');

        if (!empty($vendedor->usuario_id)) {
            $this->flash('error', 'Este vendedor ya tiene acceso al sistema.');
            $this->redirect('vendedor');
        }

        $this->crearAccesoParaVendedor((int)$id, $vendedor->nombre . ' ' . $vendedor->apellido, $vendedor->email);
        $this->flash('success', 'Acceso creado. Comparte la contraseña temporal con el vendedor.');
        $this->redirect('vendedor');
    }

    // GET /vendedor/resetPassword/{id} (genera una nueva contraseña temporal para un vendedor que ya tiene acceso)
    public function resetPassword(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $vendedor = $this->vendedor->findById((int)$id);
        if (!$vendedor || empty($vendedor->usuario_id)) {
            $this->flash('error', 'Este vendedor no tiene una cuenta de acceso todavía.');
            $this->redirect('vendedor');
        }

        $passwordPlano = $this->generarPasswordTemporal();
        $this->usuario->update((int)$vendedor->usuario_id, [
            'password' => password_hash($passwordPlano, PASSWORD_BCRYPT),
            'password_reset_required' => 1,
        ]);

        $_SESSION['temp_password']   = $passwordPlano;
        $_SESSION['temp_user_email'] = $vendedor->email;
        LogAccion::registrar('editar', 'usuario', (int)$vendedor->usuario_id, 'Contraseña reiniciada para vendedor ' . $vendedor->nombre . ' ' . $vendedor->apellido);
        $this->flash('success', 'Nueva contraseña temporal generada.');
        $this->redirect('vendedor');
    }

    // GET /vendedor/toggleAcceso/{id} (deshabilita o reactiva el login del vendedor, sin borrar la cuenta)
    public function toggleAcceso(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $vendedor = $this->vendedor->findById((int)$id);
        if (!$vendedor || empty($vendedor->usuario_id)) {
            $this->flash('error', 'Este vendedor no tiene una cuenta de acceso todavía.');
            $this->redirect('vendedor');
        }

        $cuenta = $this->usuario->findById((int)$vendedor->usuario_id);
        if (!$cuenta) {
            $this->flash('error', 'No se encontró la cuenta de acceso vinculada.');
            $this->redirect('vendedor');
        }

        $nuevoEstado = ((int)($cuenta->estado ?? 1) === 1) ? 0 : 1;
        $this->usuario->update((int)$cuenta->id, ['estado' => $nuevoEstado]);

        $accion = $nuevoEstado === 1 ? 'reactivado' : 'deshabilitado';
        LogAccion::registrar('editar', 'usuario', (int)$cuenta->id, 'Acceso ' . $accion . ' para vendedor ' . $vendedor->nombre . ' ' . $vendedor->apellido);
        $this->flash('success', 'Acceso ' . $accion . ' correctamente.');
        $this->redirect('vendedor');
    }

    // Crea la cuenta de usuario (rol vendedor) y la vincula al registro de vendedores.
    private function crearAccesoParaVendedor(int $vendedorId, string $nombreCompleto, string $email): void {
        if ($this->usuario->findByEmail($email)) {
            $this->flash('error', 'Ya existe una cuenta de usuario con ese email; no se creó el acceso.');
            return;
        }

        $passwordPlano = $this->generarPasswordTemporal();
        $nuevoUsuarioId = $this->usuario->insert([
            'nombre'                   => $nombreCompleto,
            'email'                    => $email,
            'password'                 => password_hash($passwordPlano, PASSWORD_BCRYPT),
            'rol'                      => 'vendedor',
            'estado'                   => 1,
            'password_reset_required'  => 1,
        ]);

        $this->vendedor->update($vendedorId, ['usuario_id' => (int)$nuevoUsuarioId]);
        LogAccion::registrar('crear', 'usuario', (int)$nuevoUsuarioId, 'Acceso de vendedor creado para ' . $nombreCompleto);

        $_SESSION['temp_password']   = $passwordPlano;
        $_SESSION['temp_user_email'] = $email;
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

    // GET/POST /vendedor/editar/{id}
    public function editar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $vendedor = $this->vendedor->findById((int)$id);
        if (!$vendedor) $this->redirect('vendedor');

        $errores = [];

        if ($this->isPost()) {
            $this->requireCsrf();
            $datos = [
                'nombre'   => $this->sanitize($_POST['nombre']   ?? ''),
                'apellido' => $this->sanitize($_POST['apellido'] ?? ''),
                'email'    => $this->sanitize($_POST['email']    ?? ''),
                'telefono' => $this->sanitize($_POST['telefono'] ?? ''),
                'zona'     => $this->sanitize($_POST['zona']     ?? ''),
                'comision' => (float) ($_POST['comision']        ?? 3.00),
            ];

            if (empty($datos['nombre']))   $errores[] = 'El nombre es obligatorio.';
            if (empty($datos['apellido'])) $errores[] = 'El apellido es obligatorio.';
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El email no es válido.';
            }
            if ($datos['comision'] < 0 || $datos['comision'] > 100) {
                $errores[] = 'La comisión debe estar entre 0 y 100.';
            }

            if (empty($errores)) {
                $this->vendedor->update((int)$id, $datos);
                LogAccion::registrar('editar', 'vendedor', (int)$id, $datos['nombre'] . ' ' . $datos['apellido']);
                $this->flash('success', 'Vendedor actualizado correctamente.');
                $this->redirect('vendedor');
            }
        }

        $this->render('vendedores/editar', [
            'titulo'   => 'Editar Vendedor',
            'vendedor' => $vendedor,
            'errores'  => $errores,
        ]);
    }

    // GET /vendedor/eliminar/{id}
    public function eliminar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $vendedor = $this->vendedor->findById((int)$id);

        // Si el vendedor tenía acceso al sistema (HU-27), eliminar también esa
        // cuenta para no dejar un login "huérfano" sin vendedor vinculado.
        if ($vendedor && !empty($vendedor->usuario_id)) {
            $this->usuario->delete((int)$vendedor->usuario_id);
        }

        $this->vendedor->delete((int)$id);
        LogAccion::registrar('eliminar', 'vendedor', (int)$id, $vendedor ? ($vendedor->nombre . ' ' . $vendedor->apellido) : '');
        $this->flash('success', 'Vendedor eliminado.');
        $this->redirect('vendedor');
    }

    // GET/POST /vendedor/reasignar/{id}  (HU-19: reasignación de propiedades entre vendedores)
    public function reasignar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);

        $origen = $this->vendedor->findById((int)$id);
        if (!$origen) $this->redirect('vendedor');

        $errores = [];
        $propiedadesOrigen = $this->propiedad->findWhere('vendedor_id', (int)$id);

        if ($this->isPost()) {
            $this->requireCsrf();
            $destinoId = (int) ($_POST['vendedor_destino'] ?? 0);
            $destino   = $destinoId ? $this->vendedor->findById($destinoId) : false;

            if (!$destino) {
                $errores[] = 'Debes seleccionar un vendedor destino válido.';
            } elseif ($destinoId === (int)$id) {
                $errores[] = 'El vendedor destino no puede ser el mismo que el de origen.';
            }

            if (empty($errores)) {
                foreach ($propiedadesOrigen as $prop) {
                    $this->propiedad->update((int)$prop->id, ['vendedor_id' => $destinoId]);
                }
                LogAccion::registrar('editar', 'vendedor', (int)$id,
                    count($propiedadesOrigen) . ' propiedad(es) reasignadas de ' . $origen->nombre . ' a ' . $destino->nombre);
                $this->flash('success', count($propiedadesOrigen) . ' propiedad(es) reasignadas de '
                    . $origen->nombre . ' a ' . $destino->nombre . '.');
                $this->redirect('vendedor');
            }
        }

        $otrosVendedores = array_filter($this->vendedor->findAll('nombre ASC'), fn($v) => (int)$v->id !== (int)$id);

        $this->render('vendedores/reasignar', [
            'titulo'             => 'Reasignar Propiedades',
            'origen'             => $origen,
            'propiedadesOrigen'  => $propiedadesOrigen,
            'otrosVendedores'    => $otrosVendedores,
            'errores'            => $errores,
        ]);
    }
}

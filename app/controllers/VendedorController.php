<?php
// ============================================================
//  CONTROLLER: Vendedor – CRUD
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Vendedor.php';

class VendedorController extends Controller {

    private Vendedor $vendedor;

    public function __construct() {
        $this->vendedor = new Vendedor();
    }

    // GET /vendedor
    public function index(): void {
        Middleware::auth();
        $vendedores = $this->vendedor->findAll('nombre ASC');
        $this->render('vendedores/index', [
            'titulo'     => 'Gestión de Vendedores',
            'vendedores' => $vendedores,
        ]);
    }

    // GET/POST /vendedor/crear
    public function crear(): void {
        Middleware::auth();
        $errores = [];

        if ($this->isPost()) {
            $datos = [
                'nombre'   => $this->sanitize($_POST['nombre']   ?? ''),
                'apellido' => $this->sanitize($_POST['apellido'] ?? ''),
                'email'    => $this->sanitize($_POST['email']    ?? ''),
                'telefono' => $this->sanitize($_POST['telefono'] ?? ''),
            ];

            if (empty($datos['nombre']))   $errores[] = 'El nombre es obligatorio.';
            if (empty($datos['apellido'])) $errores[] = 'El apellido es obligatorio.';
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El email no es válido.';
            }

            if (empty($errores)) {
                $this->vendedor->insert($datos);
                $this->flash('success', 'Vendedor registrado correctamente.');
                $this->redirect('vendedor');
            }
        }

        $this->render('vendedores/crear', [
            'titulo'  => 'Nuevo Vendedor',
            'errores' => $errores,
        ]);
    }

    // GET/POST /vendedor/editar/{id}
    public function editar(string $id = '0'): void {
        Middleware::auth();
        $vendedor = $this->vendedor->findById((int)$id);
        if (!$vendedor) $this->redirect('vendedor');

        $errores = [];

        if ($this->isPost()) {
            $datos = [
                'nombre'   => $this->sanitize($_POST['nombre']   ?? ''),
                'apellido' => $this->sanitize($_POST['apellido'] ?? ''),
                'email'    => $this->sanitize($_POST['email']    ?? ''),
                'telefono' => $this->sanitize($_POST['telefono'] ?? ''),
            ];

            if (empty($datos['nombre']))   $errores[] = 'El nombre es obligatorio.';
            if (empty($datos['apellido'])) $errores[] = 'El apellido es obligatorio.';
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El email no es válido.';
            }

            if (empty($errores)) {
                $this->vendedor->update((int)$id, $datos);
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
        Middleware::auth();
        $this->vendedor->delete((int)$id);
        $this->flash('success', 'Vendedor eliminado.');
        $this->redirect('vendedor');
    }
}

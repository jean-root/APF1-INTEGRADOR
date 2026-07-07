<?php
// ============================================================
//  CONTROLLER: Propiedad – CRUD completo
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Propiedad.php';
require_once APP_ROOT . '/app/models/Vendedor.php';
require_once APP_ROOT . '/app/models/LogAccion.php';

class PropiedadController extends Controller {

    private Propiedad $propiedad;
    private Vendedor  $vendedor;

    public function __construct() {
        $this->propiedad = new Propiedad();
        $this->vendedor  = new Vendedor();
    }

    // ── PÚBLICO ──────────────────────────────────────────────

    // GET /propiedad  → catálogo público
    public function index(): void {
        $filtros = [
            'tipo'         => $_GET['tipo']         ?? '',
            'habitaciones' => $_GET['habitaciones'] ?? '',
            'banos'        => $_GET['banos']        ?? '',
            'metros2_min'  => $_GET['metros2_min']  ?? '',
            'precio_min'   => $_GET['precio_min']   ?? '',
            'precio_max'   => $_GET['precio_max']   ?? '',
        ];

        $hayFiltrosAvanzados = ($filtros['habitaciones'] !== '' || $filtros['banos'] !== ''
            || $filtros['metros2_min'] !== '' || $filtros['precio_min'] !== '' || $filtros['precio_max'] !== '');

        $propiedades = ($hayFiltrosAvanzados || $filtros['tipo'] !== '')
            ? $this->propiedad->buscarConFiltros($filtros)
            : $this->propiedad->todasActivas();

        $this->render('propiedades/index', [
            'titulo'      => 'Propiedades – ' . APP_NAME,
            'propiedades' => $propiedades,
            'tipoActivo'  => $filtros['tipo'],
            'filtros'     => $filtros,
        ]);
    }

    // GET /propiedad/detalle/{id}
    public function detalle(string $id = '0'): void {
        $propiedad = $this->propiedad->detalleConVendedor((int)$id);
        if (!$propiedad) {
            $this->redirect('propiedad');
        }
        $this->render('propiedades/detalle', [
            'titulo'    => $propiedad->titulo . ' – ' . APP_NAME,
            'propiedad' => $propiedad,
        ]);
    }

    // ── ADMIN (protegidas con Middleware) ─────────────────────

    // GET /propiedad/admin  → lista admin
    public function admin(): void {
        Middleware::requireRole(['supervisor']);
        $propiedades = $this->propiedad->findAll();
        $this->render('propiedades/admin_index', [
            'titulo'      => 'Gestión de Propiedades',
            'propiedades' => $propiedades,
        ]);
    }

    // GET/POST /propiedad/crear
    public function crear(): void {
        Middleware::requireRole(['supervisor']);
        $errores = [];

        if ($this->isPost()) {
            $this->requireCsrf();
            $datos = $this->recogerDatos();
            $errores = $this->validar($datos);

            if (empty($errores)) {
                // Subir imagen
                if (!empty($_FILES['imagen']['name'])) {
                    $nombreImg = $this->propiedad->subirImagen($_FILES['imagen']);
                    if ($nombreImg) {
                        $datos['imagen'] = $nombreImg;
                    } else {
                        $errores[] = 'La imagen no es válida. Solo JPG, PNG o WEBP hasta 5MB.';
                    }
                }

                if (empty($errores)) {
                    $nuevoId = $this->propiedad->insert($datos);
                    LogAccion::registrar('crear', 'propiedad', (int)$nuevoId, $datos['titulo']);
                    $this->flash('success', 'Propiedad creada correctamente.');
                    $this->redirect('propiedad/admin');
                }
            }
        }

        $vendedores = $this->vendedor->listaParaSelect();
        $this->render('propiedades/crear', [
            'titulo'     => 'Nueva Propiedad',
            'vendedores' => $vendedores,
            'errores'    => $errores,
        ]);
    }

    // GET/POST /propiedad/editar/{id}
    public function editar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $propiedad = $this->propiedad->findById((int)$id);
        if (!$propiedad) $this->redirect('propiedad/admin');

        $errores = [];

        if ($this->isPost()) {
            $this->requireCsrf();
            $datos = $this->recogerDatos();
            $errores = $this->validar($datos);

            if (empty($errores)) {
                if (!empty($_FILES['imagen']['name'])) {
                    $nombreImg = $this->propiedad->subirImagen($_FILES['imagen']);
                    if ($nombreImg) {
                        // Eliminar imagen anterior si no es la default
                        if ($propiedad->imagen !== 'no-imagen.jpg') {
                            @unlink(UPLOAD_DIR . $propiedad->imagen);
                        }
                        $datos['imagen'] = $nombreImg;
                    } else {
                        $errores[] = 'La imagen no es válida. Solo JPG, PNG o WEBP hasta 5MB.';
                    }
                }

                if (empty($errores)) {
                    $this->propiedad->update((int)$id, $datos);
                    LogAccion::registrar('editar', 'propiedad', (int)$id, $datos['titulo']);
                    $this->flash('success', 'Propiedad actualizada correctamente.');
                    $this->redirect('propiedad/admin');
                }
            }
        }

        $vendedores = $this->vendedor->listaParaSelect();
        $this->render('propiedades/editar', [
            'titulo'     => 'Editar Propiedad',
            'propiedad'  => $propiedad,
            'vendedores' => $vendedores,
            'errores'    => $errores,
        ]);
    }

    // GET /propiedad/eliminar/{id}
    public function eliminar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $propiedad = $this->propiedad->findById((int)$id);
        if ($propiedad) {
            if ($propiedad->imagen !== 'no-imagen.jpg') {
                @unlink(UPLOAD_DIR . $propiedad->imagen);
            }
            $this->propiedad->delete((int)$id);
            LogAccion::registrar('eliminar', 'propiedad', (int)$id, $propiedad->titulo);
            $this->flash('success', 'Propiedad eliminada.');
        }
        $this->redirect('propiedad/admin');
    }

    // ── HELPERS PRIVADOS ─────────────────────────────────────

    private function recogerDatos(): array {
        return [
            'titulo'           => $this->sanitize($_POST['titulo']           ?? ''),
            'descripcion'      => $this->sanitize($_POST['descripcion']      ?? ''),
            'precio'           => (float) ($_POST['precio']                  ?? 0),
            'tipo'             => $this->sanitize($_POST['tipo']             ?? 'casa'),
            'habitaciones'     => (int) ($_POST['habitaciones']              ?? 0),
            'banos'            => (int) ($_POST['banos']                     ?? 0),
            'estacionamientos' => (int) ($_POST['estacionamientos']          ?? 0),
            'metros2'          => (float) ($_POST['metros2']                 ?? 0),
            'direccion'        => $this->sanitize($_POST['direccion']        ?? ''),
            'vendedor_id'      => (int) ($_POST['vendedor_id']               ?? 0),
            'activo'           => isset($_POST['activo']) ? 1 : 0,
            'destacado'        => isset($_POST['destacado']) ? 1 : 0,
        ];
    }

    private function validar(array $datos): array {
        $errores = [];
        if (empty($datos['titulo']))  $errores[] = 'El título es obligatorio.';
        if ($datos['precio'] <= 0)    $errores[] = 'El precio debe ser mayor a 0.';
        if (empty($datos['tipo']))    $errores[] = 'El tipo de propiedad es obligatorio.';
        return $errores;
    }
}

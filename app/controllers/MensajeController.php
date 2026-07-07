<?php
// ============================================================
//  CONTROLLER: Mensaje – Panel inbox de mensajes de prospectos (HU-22)
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Mensaje.php';
require_once APP_ROOT . '/app/models/Vendedor.php';
require_once APP_ROOT . '/app/models/LogAccion.php';

class MensajeController extends Controller {

    private Mensaje  $mensaje;
    private Vendedor $vendedor;
    private LogAccion $logAccion;

    public function __construct() {
        $this->mensaje   = new Mensaje();
        $this->vendedor  = new Vendedor();
        $this->logAccion = new LogAccion();
    }

    // GET /mensaje  → bandeja de entrada (HU-28: incluye vendedor asignado y estado del lead)
    public function index(): void {
        Middleware::requireRole(['supervisor']);

        $mensajes = $this->mensaje->todosConVendedor();

        $this->render('mensajes/index', [
            'titulo'    => 'Bandeja de Mensajes',
            'mensajes'  => $mensajes,
            'noLeidos'  => $this->mensaje->noLeidos(),
        ]);
    }

    // GET/POST /mensaje/detalle/{id}
    public function detalle(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);

        $mensaje = $this->mensaje->findById((int) $id);
        if (!$mensaje) {
            $this->redirect('mensaje');
        }

        // Al abrir el detalle se marca como leído automáticamente
        if ((int) $mensaje->leido === 0) {
            $this->mensaje->marcarLeido((int) $mensaje->id);
            $mensaje->leido = 1;
        }

        $this->render('mensajes/detalle', [
            'titulo'      => 'Mensaje de ' . $mensaje->nombre,
            'mensaje'     => $mensaje,
            'vendedores'  => $this->vendedor->findAll('nombre ASC'),
            'vendedorAsignado' => $mensaje->vendedor_id ? $this->vendedor->findById((int)$mensaje->vendedor_id) : false,
            'historial'   => $this->logAccion->historialDeEntidad('mensaje', (int)$id),
        ]);
    }

    // POST /mensaje/asignar/{id}  (HU-28: asignar el lead a un vendedor)
    public function asignar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $mensaje = $this->mensaje->findById((int) $id);
        if (!$mensaje) $this->redirect('mensaje');

        if ($this->isPost()) {
            $this->requireCsrf();
            $vendedorId = (int) ($_POST['vendedor_id'] ?? 0);
            $vendedor   = $vendedorId ? $this->vendedor->findById($vendedorId) : false;

            if ($vendedor) {
                $this->mensaje->asignar((int) $id, $vendedorId);
                LogAccion::registrar('editar', 'mensaje', (int) $id,
                    'Lead asignado a ' . $vendedor->nombre . ' ' . $vendedor->apellido);
                $this->flash('success', 'Lead asignado a ' . $vendedor->nombre . ' ' . $vendedor->apellido . '.');
            } else {
                $this->flash('error', 'Selecciona un vendedor válido.');
            }
        }

        $this->redirect('mensaje/detalle/' . $id);
    }

    // GET /mensaje/marcarLeido/{id}
    public function marcarLeido(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $this->mensaje->marcarLeido((int) $id);
        $this->flash('success', 'Mensaje marcado como leído.');
        $this->redirect('mensaje');
    }

    // GET /mensaje/eliminar/{id}
    public function eliminar(string $id = '0'): void {
        Middleware::requireRole(['supervisor']);
        $this->mensaje->delete((int) $id);
        $this->flash('success', 'Mensaje eliminado.');
        $this->redirect('mensaje');
    }
}

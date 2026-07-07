<?php
// ============================================================
//  CONTROLLER: Panel – Portal propio del Vendedor (HU-27)
//  Cierra la observación del docente: "No se incluyen flujos de
//  vendedor ni trazabilidad sobre las actividades que este debe
//  realizar." El vendedor inicia sesión con su propia cuenta
//  (rol='vendedor') y solo ve sus propiedades y sus leads.
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Vendedor.php';
require_once APP_ROOT . '/app/models/Propiedad.php';
require_once APP_ROOT . '/app/models/Mensaje.php';
require_once APP_ROOT . '/app/models/LogAccion.php';

class PanelController extends Controller {

    private Vendedor   $vendedorModel;
    private Propiedad  $propiedad;
    private Mensaje     $mensaje;
    private LogAccion  $logAccion;

    public function __construct() {
        $this->vendedorModel = new Vendedor();
        $this->propiedad     = new Propiedad();
        $this->mensaje        = new Mensaje();
        $this->logAccion     = new LogAccion();
    }

    // Ubica el registro de `vendedores` vinculado a la cuenta que inició sesión.
    // Si el vínculo no existe (dato inconsistente), cierra sesión por seguridad.
    private function vendedorActual(): object {
        $vendedor = $this->vendedorModel->porUsuarioId((int) ($_SESSION['usuario_id'] ?? 0));
        if (!$vendedor) {
            http_response_code(403);
            echo '<div style="font-family:sans-serif;text-align:center;padding:4rem">
                <h1 style="font-size:2rem;color:#111111">Cuenta sin vendedor vinculado</h1>
                <p style="font-size:1rem;color:#666">Tu usuario no está vinculado a ningún registro de vendedor. Contacta al supervisor.</p>
                <a href="' . BASE_URL . '/auth/logout" style="color:#FACC15">Cerrar sesión</a>
                </div>';
            exit;
        }
        return $vendedor;
    }

    // GET /panel  → dashboard del vendedor: sus propiedades y sus leads
    public function index(): void {
        Middleware::requireRole(['vendedor']);
        $vendedor = $this->vendedorActual();

        $propiedades = $this->propiedad->findWhere('vendedor_id', (int) $vendedor->id);
        $leads       = $this->mensaje->porVendedor((int) $vendedor->id);

        // Pequeño resumen del pipeline propio (trazabilidad de actividad)
        $resumenEstados = array_fill_keys(Mensaje::estadosValidos(), 0);
        foreach ($leads as $lead) {
            $estado = $lead->estado ?? 'nuevo';
            if (isset($resumenEstados[$estado])) {
                $resumenEstados[$estado]++;
            }
        }

        $this->render('panel/index', [
            'titulo'         => 'Mi Panel de Vendedor',
            'vendedor'       => $vendedor,
            'propiedades'    => $propiedades,
            'leads'          => $leads,
            'resumenEstados' => $resumenEstados,
        ]);
    }

    // GET /panel/mensaje/{id} → detalle de un lead propio + historial
    public function mensaje(string $id = '0'): void {
        Middleware::requireRole(['vendedor']);
        $vendedor = $this->vendedorActual();

        $lead = $this->mensaje->findById((int) $id);
        if (!$lead || (int) $lead->vendedor_id !== (int) $vendedor->id) {
            $this->redirect('panel');
        }

        $this->render('panel/mensaje', [
            'titulo'    => 'Lead de ' . $lead->nombre,
            'lead'      => $lead,
            'historial' => $this->logAccion->historialDeEntidad('mensaje', (int) $id),
        ]);
    }

    // POST /panel/cambiarEstado/{id} → el vendedor avanza su propio lead en el pipeline
    public function cambiarEstado(string $id = '0'): void {
        Middleware::requireRole(['vendedor']);
        $vendedor = $this->vendedorActual();

        $lead = $this->mensaje->findById((int) $id);
        if (!$lead || (int) $lead->vendedor_id !== (int) $vendedor->id) {
            $this->redirect('panel');
        }

        if ($this->isPost()) {
            $this->requireCsrf();
            $nuevoEstado = $this->sanitize($_POST['estado'] ?? '');

            if (in_array($nuevoEstado, Mensaje::estadosValidos(), true)) {
                $this->mensaje->cambiarEstado((int) $id, $nuevoEstado);
                LogAccion::registrar('editar', 'mensaje', (int) $id,
                    'Estado actualizado a "' . Mensaje::etiquetaEstado($nuevoEstado) . '" por ' . $vendedor->nombre);
                $this->flash('success', 'Estado del lead actualizado.');
            } else {
                $this->flash('error', 'Estado no válido.');
            }
        }

        $this->redirect('panel/mensaje/' . $id);
    }
}

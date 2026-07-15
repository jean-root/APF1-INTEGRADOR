<?php
// ============================================================
//  CONTROLLER: Bitácora de auditoría (HU-25)
//  Solo accesible para el rol admin (Administrador TI).
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/LogAccion.php';
require_once APP_ROOT . '/app/models/Auditoria.php';

class BitacoraController extends Controller {

    private LogAccion $logAccion;
    private Auditoria $auditoria;

    public function __construct() {
        $this->logAccion = new LogAccion();
        $this->auditoria = new Auditoria();
    }

    // GET /bitacora
    public function index(): void {
        Middleware::requireRole(['admin']);

        $registros = $this->logAccion->recientes(100);

        // Auditoría a nivel de BD (triggers, migración 005). Si la migración
        // todavía no se ejecutó en este entorno, no se rompe la página.
        try {
            $registrosBd = $this->auditoria->recientes(100);
        } catch (Throwable $e) {
            $registrosBd = [];
        }

        $this->render('bitacora/index', [
            'titulo'      => 'Bitácora de Auditoría',
            'registros'   => $registros,
            'registrosBd' => $registrosBd,
        ]);
    }
}

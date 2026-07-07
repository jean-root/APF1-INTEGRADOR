<?php
// ============================================================
//  CONTROLLER: Bitácora de auditoría (HU-25)
//  Solo accesible para el rol admin (Administrador TI).
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/LogAccion.php';

class BitacoraController extends Controller {

    private LogAccion $logAccion;

    public function __construct() {
        $this->logAccion = new LogAccion();
    }

    // GET /bitacora
    public function index(): void {
        Middleware::requireRole(['admin']);

        $registros = $this->logAccion->recientes(100);

        $this->render('bitacora/index', [
            'titulo'    => 'Bitácora de Auditoría',
            'registros' => $registros,
        ]);
    }
}

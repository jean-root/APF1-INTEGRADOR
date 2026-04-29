<?php
// ============================================================
//  CONTROLLER: Home – Página principal pública
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/app/models/Propiedad.php';

class HomeController extends Controller {

    private Propiedad $propiedad;

    public function __construct() {
        $this->propiedad = new Propiedad();
    }

    // GET /
    public function index(): void {
        $propiedades    = $this->propiedad->ultimas(6);
        $totalPropied   = $this->propiedad->count('activo = 1');

        $this->render('home/index', [
            'propiedades'  => $propiedades,
            'totalPropied' => $totalPropied,
            'titulo'       => 'Inicio – ' . APP_NAME,
        ]);
    }
}

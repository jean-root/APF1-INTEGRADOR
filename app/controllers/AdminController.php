<?php
// ============================================================
//  CONTROLLER: Admin – Dashboard
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Propiedad.php';
require_once APP_ROOT . '/app/models/Vendedor.php';
require_once APP_ROOT . '/app/models/Mensaje.php';

class AdminController extends Controller {

    // GET /admin/dashboard
    public function dashboard(): void {
        Middleware::auth();

        $propiedad = new Propiedad();
        $vendedor  = new Vendedor();
        $mensaje   = new Mensaje();

        $this->render('admin/dashboard', [
            'titulo'          => 'Dashboard – Panel Admin',
            'totalPropiedades'=> $propiedad->count(),
            'totalActivas'    => $propiedad->count('activo = 1'),
            'totalVendedores' => $vendedor->count(),
            'totalMensajes'   => $mensaje->count(),
            'noLeidos'        => $mensaje->noLeidos(),
            'ultimasProp'     => $propiedad->ultimas(5),
            'ultimosMensajes' => $mensaje->findAll('created_at DESC'),
        ]);
    }
}

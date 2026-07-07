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
        Middleware::requireRole(['supervisor']);

        $propiedad = new Propiedad();
        $vendedor  = new Vendedor();
        $mensaje   = new Mensaje();

        $this->render('admin/dashboard', [
            'titulo'          => 'Dashboard – Panel Supervisor',
            'totalPropiedades'=> $propiedad->count(),
            'totalActivas'    => $propiedad->count('activo = 1'),
            'totalVendedores' => $vendedor->count(),
            'totalMensajes'   => $mensaje->count(),
            'noLeidos'        => $mensaje->noLeidos(),
            'ultimasProp'     => $propiedad->ultimas(5),
            'ultimosMensajes' => $mensaje->findAll('created_at DESC'),
            // HU-26: datos para las gráficas del dashboard
            'graficaTipos'     => $propiedad->contarPorTipo(),
            'graficaMensajes'  => $this->ultimosSeisMeses($mensaje->contarPorMes()),
            'graficaVendedores'=> $vendedor->topPorPropiedades(5),
        ]);
    }

    // Completa los últimos 6 meses (incluyendo meses sin mensajes, en 0) para la gráfica de línea/barras
    private function ultimosSeisMeses(array $porMes): array {
        $mapa = [];
        foreach ($porMes as $fila) {
            $mapa[$fila->mes] = (int) $fila->total;
        }

        $resultado = [];
        for ($i = 5; $i >= 0; $i--) {
            $clave = date('Y-m', strtotime("-{$i} months"));
            $resultado[] = [
                'mes'   => $clave,
                'total' => $mapa[$clave] ?? 0,
            ];
        }
        return $resultado;
    }
}

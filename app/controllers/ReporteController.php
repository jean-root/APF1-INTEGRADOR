<?php
// ============================================================
//  CONTROLLER: Reporte — Exportación de reportes (Excel/CSV y PDF)
//  Requisito Semana 18: "Reportes (exportar en excel, pdf y gráficos
//  estadísticos)". Las gráficas estadísticas ya viven en el dashboard
//  (HU-26, Chart.js); este controlador cubre la exportación a
//  Excel (CSV, compatible nativo) y PDF (vista imprimible del navegador,
//  sin depender de librerías externas / Composer, igual que el resto
//  del proyecto).
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/app/models/Propiedad.php';
require_once APP_ROOT . '/app/models/Vendedor.php';
require_once APP_ROOT . '/app/models/Mensaje.php';

class ReporteController extends Controller {

    private Propiedad $propiedad;
    private Vendedor  $vendedor;
    private Mensaje   $mensaje;

    private const TIPOS_VALIDOS = ['propiedades', 'vendedores', 'mensajes'];

    public function __construct() {
        $this->propiedad = new Propiedad();
        $this->vendedor  = new Vendedor();
        $this->mensaje   = new Mensaje();
    }

    // GET /reporte/csv/{tipo}  → descarga .csv (se abre directo en Excel)
    public function csv(string $tipo = ''): void {
        Middleware::requireRole(['admin', 'supervisor']);

        if (!in_array($tipo, self::TIPOS_VALIDOS, true)) {
            $this->flash('error', 'Tipo de reporte no válido.');
            $this->redirect('admin/dashboard');
        }

        [$columnas, $filas] = $this->datosReporte($tipo);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $tipo . '_' . date('Ymd_His') . '.csv"');

        $out = fopen('php://output', 'w');
        // BOM UTF-8 para que Excel abra bien las tildes/ñ
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $columnas);
        foreach ($filas as $fila) {
            fputcsv($out, $fila);
        }
        fclose($out);
        exit;
    }

    // GET /reporte/pdf/{tipo} → vista imprimible (el navegador genera el PDF con "Guardar como PDF")
    public function pdf(string $tipo = ''): void {
        Middleware::requireRole(['admin', 'supervisor']);

        if (!in_array($tipo, self::TIPOS_VALIDOS, true)) {
            $this->flash('error', 'Tipo de reporte no válido.');
            $this->redirect('admin/dashboard');
        }

        [$columnas, $filas] = $this->datosReporte($tipo);

        $this->render('reportes/pdf', [
            'titulo'   => 'Reporte de ' . ucfirst($tipo),
            'tipo'     => $tipo,
            'columnas' => $columnas,
            'filas'    => $filas,
        ]);
    }

    // Arma [columnas, filas] según el tipo de reporte solicitado
    private function datosReporte(string $tipo): array {
        switch ($tipo) {
            case 'propiedades':
                $columnas = ['ID', 'Título', 'Tipo', 'Precio (S/)', 'Habitaciones', 'Baños', 'm2', 'Vendedor', 'Activo', 'Destacado', 'Fecha'];
                $filas = [];
                foreach ($this->propiedad->todasActivas() as $p) {
                    $filas[] = [
                        $p->id, $p->titulo, $p->tipo, number_format((float)$p->precio, 2, '.', ''),
                        $p->habitaciones, $p->banos, $p->metros2,
                        trim(($p->vendedor_nombre ?? '') . ' ' . ($p->vendedor_apellido ?? '')) ?: '—',
                        $p->activo ? 'Sí' : 'No', $p->destacado ? 'Sí' : 'No',
                        date('d/m/Y', strtotime($p->created_at)),
                    ];
                }
                return [$columnas, $filas];

            case 'vendedores':
                $columnas = ['ID', 'Nombre', 'Apellido', 'Email', 'Teléfono', 'Zona', 'Comisión (%)', 'Con acceso', 'Fecha registro'];
                $filas = [];
                foreach ($this->vendedor->findAll('nombre ASC') as $v) {
                    $filas[] = [
                        $v->id, $v->nombre, $v->apellido, $v->email, $v->telefono ?: '—',
                        $v->zona ?: '—', number_format((float)($v->comision ?? 0), 2, '.', ''),
                        !empty($v->usuario_id) ? 'Sí' : 'No',
                        date('d/m/Y', strtotime($v->created_at)),
                    ];
                }
                return [$columnas, $filas];

            case 'mensajes':
                $columnas = ['ID', 'Nombre', 'Email', 'Teléfono', 'Asunto', 'Estado', 'Vendedor asignado', 'Fecha'];
                $filas = [];
                foreach ($this->mensaje->todosConVendedor() as $m) {
                    $filas[] = [
                        $m->id, $m->nombre, $m->email, $m->telefono ?: '—', $m->asunto ?: '—',
                        Mensaje::etiquetaEstado($m->estado ?? 'nuevo'),
                        trim(($m->vendedor_nombre ?? '') . ' ' . ($m->vendedor_apellido ?? '')) ?: 'Sin asignar',
                        date('d/m/Y', strtotime($m->created_at)),
                    ];
                }
                return [$columnas, $filas];

            default:
                return [[], []];
        }
    }
}

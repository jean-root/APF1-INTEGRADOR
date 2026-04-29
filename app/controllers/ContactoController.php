<?php
// ============================================================
//  CONTROLLER: Contacto
// ============================================================
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/app/models/Mensaje.php';

class ContactoController extends Controller {

    private Mensaje $mensaje;

    public function __construct() {
        $this->mensaje = new Mensaje();
    }

    // GET/POST /contacto
    public function index(): void {
        $exito = false;
        $error = '';

        if ($this->isPost()) {
            $nombre   = $this->sanitize($_POST['nombre']   ?? '');
            $email    = $this->sanitize($_POST['email']    ?? '');
            $telefono = $this->sanitize($_POST['telefono'] ?? '');
            $asunto   = $this->sanitize($_POST['asunto']   ?? '');
            $mensaje  = $this->sanitize($_POST['mensaje']  ?? '');

            if (!$nombre || !$email || !$mensaje) {
                $error = 'Por favor completa los campos requeridos.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El correo electrónico no es válido.';
            } else {
                $this->mensaje->insert([
                    'nombre'   => $nombre,
                    'email'    => $email,
                    'telefono' => $telefono,
                    'asunto'   => $asunto,
                    'mensaje'  => $mensaje,
                ]);
                $exito = true;
            }
        }

        $this->render('contacto/index', [
            'titulo' => 'Contacto – ' . APP_NAME,
            'exito'  => $exito,
            'error'  => $error,
        ]);
    }
}

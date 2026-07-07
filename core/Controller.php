<?php
// ============================================================
//  CONTROLLER – Base Controller
//  Todos los controladores extienden esta clase.
// ============================================================
class Controller {

    // ----------------------------------------------------------
    //  Renderizar una vista pasándole datos
    //  $view  = 'carpeta/archivo'  (sin .php)
    //  $data  = array asociativo de variables para la vista
    // ----------------------------------------------------------
    protected function render(string $view, array $data = []): void {
        // Extraer el array como variables individuales
        extract($data);

        $viewFile = APP_ROOT . '/app/views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die('<p style="color:red;font-family:sans-serif;padding:2rem;">
                Vista no encontrada: <strong>' . htmlspecialchars($view) . '.php</strong></p>');
        }

        require_once $viewFile;
    }

    // ----------------------------------------------------------
    //  Redireccionar a una URL
    // ----------------------------------------------------------
    protected function redirect(string $url): void {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    // ----------------------------------------------------------
    //  Verificar si la petición es POST
    // ----------------------------------------------------------
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    // ----------------------------------------------------------
    //  Sanitizar input del usuario (previene XSS)
    // ----------------------------------------------------------
    protected function sanitize(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)));
    }

    // ----------------------------------------------------------
    //  Cargar un modelo
    // ----------------------------------------------------------
    protected function model(string $modelName): object {
        $modelFile = APP_ROOT . '/app/models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
        }
        return new $modelName();
    }

    // ----------------------------------------------------------
    //  Guardar mensaje flash en sesión
    // ----------------------------------------------------------
    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    // ----------------------------------------------------------
    //  Protección CSRF (CS-11)
    //  Token único por sesión, validado en cada POST.
    // ----------------------------------------------------------
    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Campo oculto listo para insertar en cualquier <form method="POST">
    protected function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($this->csrfToken()) . '">';
    }

    // Verifica el token recibido contra el de sesión (comparación segura)
    protected function csrfValido(): bool {
        $token = $_POST['csrf_token'] ?? '';
        return !empty($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Aborta con 419 si el POST no trae un token CSRF válido
    protected function requireCsrf(): void {
        if ($this->isPost() && !$this->csrfValido()) {
            http_response_code(419);
            die('<div style="font-family:sans-serif;text-align:center;padding:4rem">
                <h1 style="font-size:3rem;color:#111111">419</h1>
                <p style="font-size:1.1rem;color:#666">Tu sesión de formulario expiró o el token de seguridad no es válido. Vuelve atrás e inténtalo de nuevo.</p>
                </div>');
        }
    }
}

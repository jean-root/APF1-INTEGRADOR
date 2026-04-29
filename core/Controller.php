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
}

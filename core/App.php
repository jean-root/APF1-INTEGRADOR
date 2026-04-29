<?php
// ============================================================
//  APP (ROUTER) – Parsea la URL y despacha al Controller correcto
//  URL pattern: /controller/method/param1/param2
// ============================================================
class App {

    protected $controller = 'HomeController';
    protected string $method     = 'index';
    protected array  $params     = [];

    public function __construct() {
        $url = $this->parseUrl();

        // ── 1. Cargar Controller ──────────────────────────────
        if (!empty($url[0])) {
            $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
            $controllerFile = APP_ROOT . '/app/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                $this->notFound();
                return;
            }
        }

        require_once APP_ROOT . '/app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller();

        // ── 2. Cargar Método ─────────────────────────────────
        if (!empty($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                $this->notFound();
                return;
            }
        }

        // ── 3. Parámetros extra ───────────────────────────────
        $this->params = $url ? array_values($url) : [];

        // ── 4. Llamar al método del Controller ───────────────
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // ----------------------------------------------------------
    //  Parsear la URL desde $_GET['url']
    // ----------------------------------------------------------
    private function parseUrl(): array {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }

    // ----------------------------------------------------------
    //  Página 404
    // ----------------------------------------------------------
    private function notFound(): void {
        http_response_code(404);
        echo '<div style="font-family:sans-serif;text-align:center;padding:4rem">
            <h1 style="font-size:4rem;color:#111111">404</h1>
            <p style="font-size:1.2rem;color:#666">Página no encontrada</p>
            <a href="' . BASE_URL . '" style="color:#FACC15">← Volver al inicio</a>
            </div>';
    }
}

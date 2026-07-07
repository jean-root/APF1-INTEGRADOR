<?php
// ============================================================
//  MIDDLEWARE – Protección de rutas administrativas
//  Previene acceso no autenticado al panel admin.
//  Registra intentos fallidos en archivo de log (R6).
// ============================================================
class Middleware {

    // ----------------------------------------------------------
    //  Verificar que el usuario esté autenticado.
    //  Si no lo está, redirige al Login.
    // ----------------------------------------------------------
    public static function auth(): void {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // ----------------------------------------------------------
    //  Verificar rol permitido para la ruta.
    // ----------------------------------------------------------
    public static function requireRole(array $roles): void {
        self::auth();

        $role = $_SESSION['usuario_rol'] ?? '';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            echo '<div style="font-family:sans-serif;text-align:center;padding:4rem">
                <h1 style="font-size:3rem;color:#111111">403</h1>
                <p style="font-size:1.1rem;color:#666">Acceso no autorizado</p>
                <a href="' . BASE_URL . '" style="color:#FACC15">← Volver al inicio</a>
                </div>';
            exit;
        }
    }

    // ----------------------------------------------------------
    //  Verificar que el usuario NO esté autenticado.
    //  (Para no mostrar el login a quien ya inició sesión)
    // ----------------------------------------------------------
    public static function guest(): void {
        if (!empty($_SESSION['usuario_id'])) {
            $rol = $_SESSION['usuario_rol'] ?? 'supervisor';
            $destino = match ($rol) {
                'admin'    => '/usuario',
                'vendedor' => '/panel',
                default    => '/admin/dashboard',
            };
            header('Location: ' . BASE_URL . $destino);
            exit;
        }
    }

    // ----------------------------------------------------------
    //  Registrar intento fallido de login en archivo .log (R6)
    // ----------------------------------------------------------
    public static function logFailedLogin(string $email): void {
        $ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry  = "[{$timestamp}] FAILED LOGIN - Email: {$email} - IP: {$ip}" . PHP_EOL;

        // Crear directorio de logs si no existe
        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        file_put_contents(LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
    }
}

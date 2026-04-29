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
    //  Verificar que el usuario NO esté autenticado.
    //  (Para no mostrar el login a quien ya inició sesión)
    // ----------------------------------------------------------
    public static function guest(): void {
        if (!empty($_SESSION['usuario_id'])) {
            header('Location: ' . BASE_URL . '/admin/dashboard');
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

<?php
// ============================================================
//  ENVLOADER – Carga variables desde /.env sin depender de Composer.
//  Prioridad: variables de entorno reales del servidor (CleverCloud)
//  > archivo .env local > valores por defecto en config.php.
// ============================================================
class EnvLoader {

    public static function load(string $path): void {
        if (!is_file($path) || !is_readable($path)) {
            return; // En producción las variables ya vienen del entorno (CleverCloud).
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);
            // Quitar comillas envolventes si existen
            $value = trim($value, "\"'");

            if ($name === '') {
                continue;
            }

            // No sobrescribir variables reales ya presentes en el entorno del servidor
            if (getenv($name) === false && !isset($_ENV[$name])) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
            }
        }
    }

    // Helper: obtiene una variable de entorno con valor por defecto
    public static function get(string $name, mixed $default = null): mixed {
        $value = getenv($name);
        if ($value === false) {
            $value = $_ENV[$name] ?? null;
        }
        return $value !== null && $value !== '' ? $value : $default;
    }
}

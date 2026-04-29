<?php
// ============================================================
//  CONFIGURACIÓN GLOBAL – Bienes Raíces Framework MVC
// ============================================================

// --- BASE URL ---
// Cambia 'APF1-INTEGRADOR' si renombras la carpeta en htdocs
define('BASE_URL', 'http://localhost/APF1-INTEGRADOR/public');

// --- RUTAS ABSOLUTAS ---
define('APP_ROOT', dirname(__DIR__));
define('UPLOAD_DIR', APP_ROOT . '/public/uploads/propiedades/');
define('UPLOAD_URL', BASE_URL . '/uploads/propiedades/');
define('LOG_FILE',   APP_ROOT . '/logs/auth.log');

// --- BASE DE DATOS ---
define('DB_HOST',    'localhost');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_NAME',    'bienes_raices');
define('DB_CHARSET', 'utf8mb4');

// --- APP ---
define('APP_NAME',    'Hogar Ideal Perú');
define('APP_TAGLINE', 'Tu hogar perfecto, garantizado');

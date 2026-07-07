<?php
// ============================================================
//  PUBLIC/INDEX.PHP – Entry point del Framework MVC
//  Toda petición HTTP pasa por aquí gracias al .htaccess
// ============================================================

session_start();

// ── Cabeceras de seguridad HTTP (CS-09) ──────────────────────
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
// 'unsafe-inline' en script-src se mantiene porque algunas vistas usan
// atributos onerror="" para fallback de imágenes rotas (no ejecutan código de terceros).
// cdnjs.cloudflare.com se permite explícitamente para cargar Chart.js (HU-26, dashboard).
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com");

// Cargar configuración
require_once dirname(__DIR__) . '/config/config.php';

// Cargar núcleo del framework
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/core/Model.php';
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Middleware.php';
require_once APP_ROOT . '/core/App.php';

// Iniciar el Router
$app = new App();

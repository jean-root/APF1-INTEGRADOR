<?php
// ============================================================
//  PUBLIC/INDEX.PHP – Entry point del Framework MVC
//  Toda petición HTTP pasa por aquí gracias al .htaccess
// ============================================================

session_start();

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

<?php
// CORS para todos los requests, incluso OPTIONS
$allowedOrigins = [
    'http://localhost:5173',
    'https://cosmic-sunshine-8bd634.netlify.app',
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
}

// Preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Todo lo demás (autoload, config, router)
define('BASE_PATH', dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Core y controllers
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Response.php';
require_once BASE_PATH . '/app/controllers/CourseController.php';
require_once BASE_PATH . '/app/controllers/ModuleController.php';
require_once BASE_PATH . '/app/controllers/LessonController.php';
require_once BASE_PATH . '/app/controllers/ExamController.php';
require_once BASE_PATH . '/app/controllers/QuestionController.php';

// Rutas
require_once BASE_PATH . '/app/routes/api.php';

// Ejecutar router
Router::dispatch();
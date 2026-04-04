<?php
define('BASE_PATH', dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Cabeceras CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5174"); // tu frontend local
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
// Si usas cookies o tokens de sesión:
header("Access-Control-Allow-Credentials: true");

// ⚠ Responder a preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Core
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Response.php';

// Controllers
require_once BASE_PATH . '/app/controllers/CourseController.php';
require_once BASE_PATH . '/app/controllers/ModuleController.php';
require_once BASE_PATH . '/app/controllers/LessonController.php';
require_once BASE_PATH . '/app/controllers/ExamController.php';
require_once BASE_PATH . '/app/controllers/QuestionController.php';

// Routes
require_once BASE_PATH . '/app/routes/api.php';

// Ejecutar router
Router::dispatch();
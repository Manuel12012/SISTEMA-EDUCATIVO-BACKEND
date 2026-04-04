<?php
define('BASE_PATH', dirname(__DIR__));

// Cargar autoload y config
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// ========================================
// 1. CORS
// ========================================

// Origen permitido (localhost para desarrollo, tu dominio en producción)
$allowedOrigins = [
    "http://localhost:5173",
    "https://mi-frontend.com" // reemplaza con tu frontend de producción si lo tienes
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
}

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true"); // si usas cookies o tokens

// Responder OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ========================================
// 2. Core
// ========================================
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Response.php';

// ========================================
// 3. Controllers
// ========================================
require_once BASE_PATH . '/app/controllers/CourseController.php';
require_once BASE_PATH . '/app/controllers/ModuleController.php';
require_once BASE_PATH . '/app/controllers/LessonController.php';
require_once BASE_PATH . '/app/controllers/ExamController.php';
require_once BASE_PATH . '/app/controllers/QuestionController.php';

// ========================================
// 4. Routes
// ========================================
require_once BASE_PATH . '/app/routes/api.php';

// ========================================
// 5. Ejecutar router
// ========================================
Router::dispatch();
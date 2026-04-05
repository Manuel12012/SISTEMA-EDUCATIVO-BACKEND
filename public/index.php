<?php
// ----------------- CORS -----------------
$allowedOrigins = [
    'http://localhost:5173', // desarrollo
    'https://cosmic-sunshine-8bd634.netlify.app', // producción
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$origin = strtolower($origin);

if (in_array($origin, array_map('strtolower', $allowedOrigins))) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("Vary: Origin");
}

// ⚠ Preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ----------------- AUTOLOAD Y CONFIG -----------------
define('BASE_PATH', dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// ----------------- CORE -----------------
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Response.php';

// ----------------- CONTROLLERS -----------------
require_once BASE_PATH . '/app/controllers/BadgeController.php';
require_once BASE_PATH . '/app/controllers/CourseController.php';
require_once BASE_PATH . '/app/controllers/ExamController.php';
require_once BASE_PATH . '/app/controllers/ExamOptionController.php';
require_once BASE_PATH . '/app/controllers/ExamResultController.php';
require_once BASE_PATH . '/app/controllers/LessonController.php';
require_once BASE_PATH . '/app/controllers/ModuleController.php';
require_once BASE_PATH . '/app/controllers/PointHistoryController.php';
require_once BASE_PATH . '/app/controllers/QuestionController.php';
require_once BASE_PATH . '/app/controllers/UserBadgeController.php';
require_once BASE_PATH . '/app/controllers/UserController.php';
require_once BASE_PATH . '/app/controllers/EnrollmentController.php';

// ----------------- MIDDLEWARES -----------------
require_once BASE_PATH . '/app/middleware/AuthMiddleware.php';
require_once BASE_PATH . '/app/middleware/RoleMiddleware.php';

// ----------------- RUTAS -----------------
require_once BASE_PATH . '/app/routes/api.php';

// ----------------- EJECUTAR ROUTER -----------------
Router::dispatch();
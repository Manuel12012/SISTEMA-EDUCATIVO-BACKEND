<?php

define('BASE_PATH', dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// ORÍGENES PERMITIDOS
$allowedOrigins = [
    'http://localhost:5173',  // Origen de desarrollo
    'https://sistema-educativo-backend-production.up.railway.app',  // Origen de producción
    // Puedes agregar más orígenes aquí si es necesario.
];

// Verificamos si el origen de la solicitud es uno de los permitidos
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = strtolower($_SERVER['HTTP_ORIGIN']); // Convertir a minúsculas
    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    }
}

// Cabeceras CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si usas cookies o tokens de sesión:
header("Access-Control-Allow-Credentials: true");

// ⚠ Responder a preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Terminamos la ejecución aquí para las solicitudes OPTIONS
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

?>
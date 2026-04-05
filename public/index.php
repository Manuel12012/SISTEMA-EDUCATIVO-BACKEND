<?php
define('BASE_PATH', dirname(__DIR__));
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

// Permitir CORS
$allowedOrigins = [
    'https://localhost:3000', // Tu frontend local
    'https://mi-frontend-produccion.com' // Cambia a tu dominio de producción
];

// Verificar si el origen de la solicitud es permitido
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Si es una solicitud OPTIONS (preflight), responder con 200 OK y finalizar aquí
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Si no es OPTIONS, proceder con la lógica normal
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Response.php';

// Cargar controladores
require_once BASE_PATH . '/app/controllers/CourseController.php';
// (otros controladores)

// Cargar rutas
require_once BASE_PATH . '/app/routes/api.php';

// Ejecutar router
Router::dispatch();
<?php
define('BASE_PATH', dirname(__DIR__));  // Define la ruta base del proyecto
require_once __DIR__ . '/../vendor/autoload.php';  // Carga los autoloaders de Composer
require_once __DIR__ . '/../config.php';  // Configuración adicional (si la tienes)

// Permitir CORS (aquí definimos los orígenes permitidos)
$allowedOrigins = [
    'https://localhost:3000',  // Durante el desarrollo
    'https://sistema-educativo-frontend.com',  // Cambia por tu dominio de producción
];

// Verificar si el origen de la solicitud está en la lista permitida
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}

// Cabeceras de CORS
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization");  // Cabeceras permitidas
header("Access-Control-Allow-Credentials: true");  // Habilitar credenciales si usas JWT o cookies

// Si la solicitud es un preflight (OPTIONS), responder con 200 OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();  // Finaliza aquí la ejecución si es un preflight
}

// Core
require_once BASE_PATH . '/app/core/Router.php';  // Cargar el router
require_once BASE_PATH . '/app/core/Response.php';  // Cargar el response

// Cargar los controladores
require_once BASE_PATH . '/app/controllers/CourseController.php';
require_once BASE_PATH . '/app/controllers/ModuleController.php';
require_once BASE_PATH . '/app/controllers/LessonController.php';
require_once BASE_PATH . '/app/controllers/ExamController.php';
require_once BASE_PATH . '/app/controllers/QuestionController.php';

// Cargar las rutas del API
require_once BASE_PATH . '/app/routes/api.php';

// Ejecutar el router
Router::dispatch();  // Aquí se ejecutan las rutas según el endpoint solicitado
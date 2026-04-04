<?php

// CORS GLOBAL (ANTES DE TODO)
header("Access-Control-Allow-Origin: http://localhost:5174");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// RESPUESTA AL PREFLIGHT
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo "OK";
    exit();
}

// Si el archivo existe, servirlo normalmente
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . "/public" . $path;

if ($path !== '/' && file_exists($file)) {
    return false;
}

// TODO pasa por index.php
require __DIR__ . '/public/index.php';
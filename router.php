<?php
// router.php

// CORS global
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Responder preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo "OK";
    exit();
}

// Ruta normal de PHP
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$file = __DIR__ . "/public" . $path;

// Si existe archivo físico → dejar que PHP lo sirva
if ($path !== '/' && file_exists($file)) {
    return false;
}

// Todo lo demás va a index.php
require __DIR__ . '/public/index.php';
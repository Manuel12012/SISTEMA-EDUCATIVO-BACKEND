<?php
class Database {
    public static function connect() {
        // Intenta obtener variables de entorno, si no existen, usa valores por defecto que funcionan en Railway
        $host = getenv('MYSQLHOST') ?: $_ENV['MYSQLHOST'] ?? 'mysql.railway.internal';
        $db   = getenv('MYSQLDATABASE') ?: $_ENV['MYSQLDATABASE'] ?? 'railway';
        $user = getenv('MYSQLUSER') ?: $_ENV['MYSQLUSER'] ?? 'root';
        $pass = getenv('MYSQLPASSWORD') ?: $_ENV['MYSQLPASSWORD'] ?? 'svgZMCxejMssJCljNDDucZjTOFKmnlGc';
        $port = getenv('MYSQLPORT') ?: $_ENV['MYSQLPORT'] ?? 3306;

        if (!$host || !$db || !$user || !$pass) {
            die(json_encode([
                "error" => "Variables de entorno de MySQL no encontradas"
            ]));
        }

        try {
            return new PDO(
                "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die(json_encode([
                "error" => "Error de conexión: " . $e->getMessage()
            ]));
        }
    }
}
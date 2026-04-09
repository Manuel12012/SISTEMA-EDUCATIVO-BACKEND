<?php
class Database {
    public static function connect() {

        $host = getenv('MYSQLHOST') ?: ($_ENV['MYSQLHOST'] ?? "127.0.0.1");
        $db   = getenv('MYSQLDATABASE') ?: ($_ENV['MYSQLDATABASE'] ?? "plataforma-educativa");
        $user = getenv('MYSQLUSER') ?: ($_ENV['MYSQLUSER'] ?? "root");
        $pass = getenv('MYSQLPASSWORD') ?: ($_ENV['MYSQLPASSWORD'] ?? "");
        $port = getenv('MYSQLPORT') ?: ($_ENV['MYSQLPORT'] ?? 3306);

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
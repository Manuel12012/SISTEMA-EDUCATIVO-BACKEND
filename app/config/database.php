<?php
class Database {
    public static function connect() {
        $host = $_ENV['MYSQLHOST'] ?? 'default_host';
        $db   = $_ENV['MYSQLDATABASE'] ?? 'default_db';
        $user = $_ENV['MYSQLUSER'] ?? 'default_user';
        $pass = $_ENV['MYSQLPASSWORD'] ?? 'default_pass';
        $port = $_ENV['MYSQLPORT'] ?? 3306;

        try {
            return new PDO(
                "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
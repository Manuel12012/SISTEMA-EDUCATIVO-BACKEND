<?php
class Database {
    public static function connect() {
        $host = getenv("MYSQL_HOST");        // <- usar nombre exacto
        $db   = getenv("MYSQL_DATABASE");    // <- nombre exacto
        $user = getenv("MYSQL_USER");
        $pass = getenv("MYSQL_PASSWORD");
        $port = getenv("MYSQL_PORT") ?: 3306;

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
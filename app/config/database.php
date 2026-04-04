<?php
class Database {
    public static function connect() {
        $host = getenv("MYSQL_HOST");        // Host desde Railway
        $db   = getenv("MYSQL_DATABASE");    // Nombre DB
        $user = getenv("MYSQL_USER");        // Usuario
        $pass = getenv("MYSQL_PASSWORD");    // Contraseña
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
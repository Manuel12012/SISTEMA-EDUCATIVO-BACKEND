<?php
class Database {
    public static function connect() {
        $host = getenv("MYSQLHOST");        // Host desde Railway
        $db   = getenv("MYSQLDATABASE");    // Nombre DB
        $user = getenv("MYSQLUSER");        // Usuario
        $pass = getenv("MYSQLPASSWORD");    // Contraseña
        $port = getenv("MYSQLPORT") ?: 3306;

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
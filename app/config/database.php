<?php
class Database {
    public static function connect() {
        $host = getenv("MYSQLHOST");
        $db   = getenv("MYSQLDATABASE");
        $user = getenv("MYSQLUSER");
        $pass = getenv("MYSQLPASSWORD");
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
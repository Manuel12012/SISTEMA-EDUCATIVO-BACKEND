<?php
class Database {
    public static function connect() {
        $host = "mysql.railway.internal";
        $db   = "railway";
        $user = "root";
        $pass = "svgZMCxejMssJCljNDDucZjTOFKmnlGc";
        $port = 3306;

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
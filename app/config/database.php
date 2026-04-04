<?php

class Database{
    public static function connect(){
        $host = getenv("MYSQLHOST");
        $db = getenv("MYSQLDATABASE");
        $user = getenv("MYSQLUSER");
        $pass = getenv("MYSQLPASSWORD");

        return new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
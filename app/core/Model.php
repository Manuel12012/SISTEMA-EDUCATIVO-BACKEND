<?php

require_once __DIR__ . "/../config/database.php";

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = "id";

    protected static ?PDO $db = null;

    protected static function db(): PDO
    {
        if (self::$db === null) {
            self::$db = Database::connect();
        }

        return self::$db;
    }

    public static function paginate(int $page = 1, int $limit = 10)
    {
        $db = static::db();

        $offset = ($page - 1) * $limit;

        $stmt = $db->prepare("
        SELECT * FROM " . static::$table . "
        LIMIT :limit OFFSET :offset
    ");

        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $db->query("SELECT COUNT(*) FROM " . static::$table)->fetchColumn();

        return [
            "data" => $data,
            "pagination" => [
                "page" => $page,
                "limit" => $limit,
                "total" => $total,
                "total_pages" => ceil($total / $limit)
            ]
        ];
    }
    public static function all()
    {
        $db = Database::connect();
        $stmt = $db->query("SELECT * FROM " . static::$table);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public static function find(int $id)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM " . static::$table . " WHERE" . static::$primaryKey . "=:id"
        );
        $stmt->execute(["id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

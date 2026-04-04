<?php

require_once __DIR__ . '/../core/Model.php';

class PointsHistory extends Model {
    protected static string $table = 'points_history';

    public static function getByUser(int $userId) {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM points_history WHERE user_id = :id ORDER BY created_at DESC"
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public static function find(int $pointsHistoryId): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM points_history WHERE id = :id"
        );
        $stmt->execute(["id" => $pointsHistoryId]);
        // al tratarse de un find solo se trae un resultado por eso se usar fetch
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function create($data)
    { // $data contiene los datos de la pregunta a insertar
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO points_history (user_id, puntos, motivo, created_at)
                    VALUES (:user_id, :puntos, :motivo, :created_at)"
        );
        $stmt->execute([
            "user_id" => $data["user_id"],
            "puntos" => $data["puntos"],
            "motivo" => $data["motivo"],
            "created_at" => $data["created_at"]
        ]);
        // retornamos con lastInsertId porque sera de manera auto_increment
        return (int) $db->lastInsertId();
    }

    public static function update($pointsHistoryId, $data)
    { // creamos dos variables questionId y data
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE points_history SET user_id = :user_id, puntos = :puntos,
                motivo = :motivo, created_at = :created_at WHERE id = :id"
        ); // retornamos igual un stmt y lo almacenamos en un array $data y tambien el $questionId
        return $stmt->execute([
            "user_id" => $data["user_id"],
            "puntos" => $data["puntos"],
            "motivo" => $data["motivo"],
            "created_at" => $data["created_at"],
            "id" => $pointsHistoryId
        ]);
    }

    public static function delete($pointsHistoryId)
    { // le pasamos como parametro questionId
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM points_history WHERE id = :id"
        );
        // no devolveremos nada, ya que se borro la pregunta
        return $stmt->execute(["id" => $pointsHistoryId]);
    }

}
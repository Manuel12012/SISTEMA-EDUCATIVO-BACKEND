<?php
require_once __DIR__ . '/../core/Model.php';

class ExamAttempt extends Model {

    protected static string $table = "exam_attempts";

    public static function createAttempt($exam_id, $user_id, $duracion_minutos){

        $db = Database::connect();

        $started_at = date("Y-m-d H:i:s");
        $expires_at = date("Y-m-d H:i:s", strtotime("+$duracion_minutos minutes"));

        $stmt = $db->prepare("
            INSERT INTO exam_attempts 
            (exam_id, user_id, started_at, expires_at)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $exam_id,
            $user_id,
            $started_at,
            $expires_at
        ]);

        return $db->lastInsertId();
    }

    public static function getActiveAttempt($exam_id, $user_id){

        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * FROM exam_attempts
            WHERE exam_id = ?
            AND user_id = ?
            AND finished = 0
            LIMIT 1
        ");

        $stmt->execute([$exam_id, $user_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
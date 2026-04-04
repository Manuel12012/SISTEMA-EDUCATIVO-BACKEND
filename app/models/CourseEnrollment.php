<?php

require_once __DIR__ . '/../core/Model.php';

class CourseEnrollment extends Model
{
    protected static string $table = 'course_enrollments';

public static function enroll(int $userId, int $courseId)
{
    $db = Database::connect();

        // Revisar si ya está inscrito
        $check = $db->prepare(
            "SELECT id FROM course_enrollments
             WHERE user_id = :user_id AND course_id = :course_id"
        );
        $check->execute([
            ":user_id" => $userId,
            ":course_id" => $courseId
        ]);

        if ($check->fetch()) {
            return false; // ya inscrito
        }

        // Insertar nueva inscripción
        $stmt = $db->prepare(
            "INSERT INTO course_enrollments (user_id, course_id)
             VALUES (:user_id, :course_id)"
        );

        if ($stmt->execute([
            ":user_id" => $userId,
            ":course_id" => $courseId
        ])) {
            return (int) $db->lastInsertId();
        }

        return false;
}

    public static function getUserCourses(int $userId)
    {
        $db = Database::connect();

        $stmt = $db->prepare(
            "SELECT c.* 
                FROM courses c
                INNER JOIN course_enrollments ce 
                ON c.id = ce.course_id
                WHERE ce.user_id = :user_id"
        );

        $stmt->execute([
            "user_id" => $userId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getStudentsByCourse($courseId) {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT u.id, u.nombre, u.email
            FROM users u
            JOIN course_enrollments ce ON ce.user_id = u.id
            WHERE ce.course_id = ?
            AND ce.status = 'active'
        ");

        $stmt->execute([$courseId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

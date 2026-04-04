<?php

require_once __DIR__ . '/../models/CourseEnrollment.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../core/Response.php';

class EnrollmentController
{
 public static function enroll()
    {
        // Leer JSON del body
        $data = json_decode(file_get_contents('php://input'), true);

        $userId = $data['user_id'] ?? null;
        $courseId = $data['course_id'] ?? null;

        // Validaciones
        if (empty($userId) || empty($courseId)) {
            Response::json(["error" => "Datos incompletos"], 400);
            return;
        }

        if (!is_numeric($userId) || !is_numeric($courseId)) {
            Response::json(["error" => "IDs invalidos"], 400);
            return;
        }

        // Crear inscripción
        $enrollmentId = CourseEnrollment::enroll((int)$userId, (int)$courseId);

        if (!$enrollmentId) {
            Response::json(["error" => "El usuario ya está inscrito o no se pudo crear"], 409);
            return;
        }

        Response::json([
            "message" => "Inscripción exitosa",
            "id" => $enrollmentId
        ], 201);
    }

    public static function myCourses($userId)
    {
        if (!is_numeric($userId)) {
            Response::json([
                "error" => "ID invalido"
            ], 400);
            return;
        }

        $courses = CourseEnrollment::getUserCourses((int)$userId);

        Response::json($courses);
    }

    public static function getStudentsByCourse($courseId)
    {
        if (!is_numeric($courseId)) {
            Response::json([
                "error" => "ID del curso inválido"
            ], 400);
            return;
        }
    
        $students = CourseEnrollment::getStudentsByCourse((int)$courseId);
    
        Response::json([
            "message" => "Students retrieved successfully",
            "data" => $students
        ], 200);
    }
}

<?php

require_once __DIR__ . '/../models/Exam.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/ExamResult.php';
require_once __DIR__ . '/../models/ExamAttempt.php';

use App\Middleware\AuthMiddleware;
use App\helpers\Validator;

class ExamController
{

    public static function index()
    {
        $titulo = $_GET["titulo"] ?? null;

        $exams = $titulo ? Exam::getByTitle($titulo) : Exam::allWithQuestionCount();

        Response::json($exams);
    }

    public static function getQuestionsByExam($examId)
    {

        Validator::validateId($examId);

        $getQuestions = Exam::getQuestionsByExam($examId);

        if (!$getQuestions) {
            Response::json([]);
            return;
        }

        Response::json($getQuestions);
    }

    public static function allWithQuestionCount()
    {
        $exams = Exam::allWithQuestionCount();

        Validator::emptyCollection($exams, "Examenes");

        Response::json($exams);
    }

    public static function show($examId)
    {
        Validator::validateId($examId);

        // traemos un examen mediante su id
        $exam = Exam::find((int)$examId);

        Validator::notFound($exam, "Examen");
        //traemos las preguntas por el id del examen
        $questions = Question::getByExam($examId);

        // iteramos, por cada pregunta las opciones seran iguales a traernos las opciones
        // por el id de la pregunta y le pasamos el id de la pregunta(question)
        foreach ($questions as &$q) {
            $q['options'] = ExamOption::getByQuestion($q['id']);
        }

        // mandamos un json con los valores del examen y questions
        Response::json([
            'exam' => $exam,
            'questions' => $questions
        ]);
    }

    public static function store($data)
    {
        // 1Verificar JWT y obtener el payload
        $userId = AuthMiddleware::getUserId();

        Validator::notFound($userId, "Usuario");
        // Validar datos del examen
        if (
            empty($data["course_id"]) ||
            empty($data["titulo"]) ||
            empty($data["duracion_minutos"])
        ) {
            Response::json(["error" => "Datos incompletos"], 400);
            exit;
        }

        // Agregar el ID del usuario al array
        $data['created_by'] = $userId;

        // 4Crear el examen usando el modelo
        try {
            $examId = Exam::create($data);
        } catch (\PDOException $e) {
            Response::json([
                "error" => "Error en la base de datos",
                "detalle" => $e->getMessage()
            ], 500);
            exit;
        }

        // Responder al cliente
        Response::json([
            "message" => "Examen creado",
            "id" => $examId
        ], 201);
    }

    public static function update($examId, $data)
    {

        Validator::validateId($examId);

        $exam = Exam::find($examId);

        Validator::notFound($exam, "Examen");

        $updated = Exam::update($examId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar el examen"
            ], 404);
            return;
        }

        Response::json([
            "message" => "Examen actualizado"
        ]);
    }

    public static function destroy($examId)
    {
        Validator::validateId($examId);

        $exam = Exam::find($examId);

        Validator::notFound($exam, "Examen");

        Exam::delete($examId);

        Response::json([
            "message" => "Examen eliminado"
        ]);
    }

    public static function results($examId)
    {
        Validator::validateId($examId);

        $examResult = ExamResult::getByExam($examId);
        Response::json([
            "examResult" => $examResult
        ]);
    }

    public static function submit($examId, $data = null)
    {

        $userId = AuthMiddleware::getUserId();

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            Response::json([
                "error" => "No se recibieron datos"
            ], 400);
            return;
        }

        Validator::validateId($examId);

        $exam = Exam::find($examId);

        Validator::notFound($exam, "Examen");

        if (empty($data["answers"]) || !is_array($data["answers"])) {
            Response::json(["error" => "Respuestas invalidas"], 400);
            return;
        }

        $exam = Exam::find($examId);

        Validator::notFound($exam, "Examen");

        $result = ExamResult::createFromSubmission(
            $examId,
            $userId,
            $data["answers"]
        );

        Response::json([
            "message" => "Examen enviado correctamente",
            "result" => $result
        ], 201);
    }

    public static function take($examId)
    {

        Validator::validateId($examId);

        $userId = AuthMiddleware::getUserId();

        Validator::notFound($userId, "Usuario");

        $alreadyTaken = ExamResult::existsByUser($examId, $userId);

        if ($alreadyTaken) {
            Response::json([
                "error" => "Ya rendiste este examen"
            ], 403);
            return;
        }

        Validator::validateId($examId);

        $exam = Exam::findActive($examId);

        Validator::notFound($exam, "Examen");

        // 🔥 verificar intento activo
        $attempt = ExamAttempt::getActiveAttempt($examId, $userId);

        if (!$attempt) {

            ExamAttempt::createAttempt(
                $examId,
                $userId,
                $exam["duracion_minutos"]
            );

            $attempt = ExamAttempt::getActiveAttempt($examId, $userId);
        }

        // Obtener preguntas
        $questions = Question::getByExam($examId);

        $filteredQuestions = [];

        foreach ($questions as $q) {

            $options = ExamOption::getByQuestion($q['id']);

            if (empty($options)) {
                continue;
            }

            unset($q['correct_option_id']);
            unset($q['exam_id']);

            foreach ($options as &$option) {
                unset($option['is_correct']);
            }

            $q['options'] = $options;

            $filteredQuestions[] = $q;
        }

        Response::json([
            "exam" => $exam,
            "questions" => $filteredQuestions,
            "expires_at" => $attempt["expires_at"] // 🔥 tiempo real
        ]);
    }
    public static function getByCourse($courseId)
    {
        $userId = AuthMiddleware::getUserId();

        Validator::validateId($courseId);

        $courses = Course::find($courseId);

        Validator::notFound($courses, "Curso");

        $exams = Exam::getExamByCourse($courseId, $userId);

        Response::json($exams);
    }
}

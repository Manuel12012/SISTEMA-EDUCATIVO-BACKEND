<?php

require_once __DIR__ . '/../models/Exam.php';
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/ExamResult.php';


class ExamController
{

    public static function index()
    {
        $exams = Exam::all();

        if (empty($exams)) {
            Response::json([
                "error" => "No se encontro el examen"
            ], 404);
            return;
        }
        Response::json($exams);
    }


    public static function getQuestionsByExam($examId)
    {

        if (!is_numeric($examId)) {
            Response::json(
                [
                    "error" => "ID de examen invalido"
                ],
                404
            );
            return;
        }

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

        if (empty($exams)) {
            Response::json([
                "error" => "No se encontro el examen"
            ], 404);
            return;
        }
        Response::json($exams);
    }

    public static function show($examId)
    {
        if (!is_numeric($examId)) {
            Response::json([
                "error" => "ID de examen invalido"
            ], status: 404);
            return;
        }
        // traemos un examen mediante su id
        $exam = Exam::find((int)$examId);

        if (!$exam) {
            Response::json(
                [
                    "error" => "Examen no encontrado"
                ],
                404
            );
            return;
        }
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
        if (
            empty($data["course_id"]) ||
            empty($data["titulo"]) ||
            empty($data["duracion_minutos"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ], 400);
            exit;
        }

        $exam = Exam::create($data);

        if (!$exam) {
            Response::json([
                "error" => "No se pudo crear el examen"
            ], 500);
            return;
        }

        Response::json([
            "message" => "Examen creado",
            "id" => $exam
        ], 201);
    }

    public static function update($examId, $data)
    {
        if (!is_numeric($examId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $exam = Exam::find($examId);

        if (!$exam) {
            Response::json([
                "error" => "Examen no encontrado"
            ], 404);
            return;
        }

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
        if (!is_numeric($examId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $exam = Exam::find($examId);

        if (!$exam) {
            Response::json([
                "error" => "No se pudo encontrar el examen"
            ], 404);
            return;
        }

        Exam::delete($examId);

        Response::json([
            "message" => "Examen eliminado"
        ]);
    }

    public static function results($examId)
    {
        if (!is_numeric($examId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            exit;
        }

        $examResult = ExamResult::getByExam($examId);
        Response::json([
            "examResult" => $examResult
        ]);
    }

public static function submit($examId, $data = null)
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        Response::json([
            "error" => "No se recibieron datos"
        ], 400);
        return;
    }

    if (!is_numeric($examId)) {
        Response::json(["error" => "ID invalido"], 400);
        return;
    }

    if (empty($data["answers"]) || !is_array($data["answers"])) {
        Response::json(["error" => "Respuestas invalidas"], 400);
        return;
    }

    $exam = Exam::find($examId);

    if (!$exam) {
        Response::json(["error" => "Examen no encontrado"], 404);
        return;
    }

    $userId = 1; // temporal

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
        if (!is_numeric($examId)) {
            Response::json([
                "error" => "ID invalido"
            ], 400);
            return;
        }

        $exam = Exam::findActive($examId);

        if (!$exam) {
            Response::json([
                "error" => "Examen no encontrado"
            ], 404);
            return;
        }

        // Obtener preguntas
        $questions = Question::getByExam($examId);

        $filteredQuestions = [];

        $filteredQuestions = [];

        foreach ($questions as $q) {

            $options = ExamOption::getByQuestion($q['id']);

            if (empty($options)) {
                continue;
            }

            // 🔥 Eliminar datos sensibles de la pregunta
            unset($q['correct_option_id']);
            unset($q['exam_id']);

            // 🔥 Eliminar is_correct de cada opción
            foreach ($options as &$option) {
                unset($option['is_correct']);
            }

            $q['options'] = $options;

            $filteredQuestions[] = $q;
        }

        Response::json([
            "exam" => $exam,
            "questions" => $filteredQuestions
        ]);
    }

    public static function getByCourse($courseId){

    if(!is_numeric($courseId)){
        Response::json([
            "error" => "Id del curso invalido"
        ],400);
        return;
    }

    $courses = Course::find($courseId);

    if(!$courses){
        Response::json([
            "error" => "Curso no encontrado"
        ],404);
        return;
    }

    $exams = Exam::getExamByCourse($courseId);
    Response::json($exams);
    }
}

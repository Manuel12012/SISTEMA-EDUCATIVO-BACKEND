<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/ExamResult.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Exam.php';
class ExamResultController
{

    public static function index()
    {
        $examResult = ExamResult::all();


        if (empty($examResult)) {
            Response::json([], 200);
            return;
        }
        Response::json($examResult);
    }

    public static function show($examResultId)
    {
        Validator::validateId($examResultId);
        $examResult = ExamResult::find($examResultId);

        Validator::notFound($examResult, "Resultado");
        Response::json($examResult);
    }


    public static function update($examResultId, $data)
    {
        Validator::validateId($examResultId);
        $examResult = ExamResult::find($examResultId);

        Validator::notFound($examResult, "Resultado");

        $updated = ExamResult::update($examResultId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            return;
        }

        Response::json([
            "message" => "Resultado actualizado"
        ]);
    }

    public static function destroy($examResultId)
    {
        Validator::validateId($examResultId);
        $examResult = ExamResult::find($examResultId);

        Validator::notFound($examResult, "Resultado");
        ExamResult::delete($examResultId);

        Response::json([
            "message" => "Resultado eliminado"
        ]);
    }

    public static function getByUserAndExam($userId, $examId)
    {
        Validator::validateId($userId);
        // accedemos al recurso 
        $user = User::find($userId);

        Validator::notFound($user, "Usuario");

        Validator::validateId($examId);
        $exam = Exam::find($examId);

        Validator::notFound($exam, "Examen");

        $examResult = ExamResult::getByUserAndExam($userId, $examId);

        Validator::notFound($examResult, "Resultado");

        Response::json([
            "user" => $user,
            "exam" => $exam,
            "examResult" => $examResult
        ]);
    }
}

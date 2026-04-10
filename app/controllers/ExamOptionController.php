<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';

class ExamOptionController
{
    public static function index()
    {
        $examOption = ExamOption::all();
        Validator::emptyCollection($examOption, "Opciones");
        Response::json($examOption);
    }

    public static function show($examOptionId)
    {
        Validator::validateId($examOptionId);
        $examOption = ExamOption::find($examOptionId);

        Validator::notFound($examOption, "Opcion");
        Response::json([
            "examOption" => $examOption
        ]);
    }

    public static function store($data)
    {
        if (
            !isset($data["question_id"]) ||
            empty($data["opcion"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ], 400);
            exit;
        }

        $examOption = ExamOption::create($data);

        if (!$examOption) {
            Response::json([
                "error" => "No se pudo crear la opcion de la pregunta"
            ], 500);
            return;
        }

        Response::json([
            "message" => "Opcion creada",
            "id" => $examOption
        ], 201);
    }

    public static function update($examOptionId, $data)
    {
        Validator::validateId($examOptionId);
        $examOption = ExamOption::find($examOptionId);

        Validator::notFound($examOption, "Opcion");
        $updated = ExamOption::update($examOptionId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            return;
        }

        Response::json([
            "message" => "Opcion actualizada"
        ]);
    }

    public static function destroy($examOptionId)
    {
        Validator::validateId($examOptionId);
        $examOption = ExamOption::find($examOptionId);

        Validator::notFound($examOption, "Opcion");
        ExamOption::delete($examOptionId);

        Response::json([
            "message" => "Opcion eliminada"
        ]);
    }

    public static function getByQuestion($questionId)
    {
        Validator::validateId($questionId);
        $options = ExamOption::getByQuestion((int)$questionId);
        if (empty($options)) {
            Response::json([]);
            return;
        }

        Response::json($options);
    }
}

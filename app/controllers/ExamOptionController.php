<?php
require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';

class ExamOptionController
{
    public static function index()
    {
        $examOption = ExamOption::all();

        if (empty($examOption)) {
            Response::json([
                "error" => "No se encontro la opcion de la pregunta"
            ], 404);
            return;
        }
        Response::json($examOption);
    }

    public static function show($examOptionId)
    {
        if (!is_numeric($examOptionId)) {
            Response::json([
                "error" => "Id de la opcion del examen invalido"
            ], 400);
            return;
        }

        $examOption = ExamOption::find($examOptionId);

        if (!$examOption) {
            Response::json([
                "error" => "Opcion de examen no encontrada"
            ], 404);
            return;
        }
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
        if (!is_numeric($examOptionId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $examOption = ExamOption::find($examOptionId);

        if (!$examOption) {
            Response::json([
                "error" => "Opcion no encontrada"
            ], 404);
            return;
        }

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
        if (!is_numeric($examOptionId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $examOption = ExamOption::find($examOptionId);

        if (!$examOption) {
            Response::json([
                "error" => "No se pudo encontrar la opcion"
            ], 404);
            return;
        }

        ExamOption::delete($examOptionId);

        Response::json([
            "message" => "Opcion eliminada"
        ]);
    }

    public static function getByQuestion($questionId)
    {
        if (!is_numeric($questionId)) {
            Response::json([
                "error" => "ID de pregunta invalido"
            ], 400);
            return;
        }

        $options = ExamOption::getByQuestion((int)$questionId);
        if (empty($options)) {
            Response::json([]);
            return;
        }

        Response::json($options);
    }
}

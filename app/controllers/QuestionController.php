<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';

class QuestionController
{
    public static function allWithOptionsCount()
    {
        $options = Question::allWithOptionsCount();

        if (empty($options)) {
            Response::json([
                "error" => "No se encontro el examen"
            ], 404);
            return;
        }
        Response::json($options);
    }

    public static function getOptionsByQuestions($questionId)
    {
        if (!is_numeric($questionId)) {
            Response::json([
                "error" => "Id de la pregunta invalido"
            ], 400);
            return;
        }

        $OptionsByQuestion = Question::getOptionsByQuestions($questionId);

        if (!$OptionsByQuestion) {
            Response::json([
                "error" => "Opcion no encontrada"
            ], 404);
        }

        Response::json($OptionsByQuestion);
    }

    public static function show($questionId)
    {
        if (!is_numeric($questionId)) {
            Response::json([
                "error" => "Id de la pregunta invalido"
            ], 400);
            return;
        }

        $question = Question::find($questionId);

        if (!$question) {
            Response::json([
                "error" => "Pregunta no encontrada"
            ], 404);
            return;
        }

        $examOptions = ExamOption::getByQuestion($questionId);

        Response::json([
            "question" => $question,
            "examOptions" => $examOptions
        ]);
    }

    public static function store($data)
    { // $data viene de create donde 
        // contiene los datos de la pregunta

        // si no existen esos campos entonces mandamos un response con datos incompletos
        if (
            empty($data["exam_id"]) ||
            empty($data["pregunta"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ], 400);
            return;
        }

        // llamamos la funcion create y la almacenamos en question
        $question = Question::create($data);

        // si question no existe entonces decimos que no se pudo crear la pregunta
        if (!$question) {
            Response::json([
                "error" => "No se pudo crear la pregunta"
            ], 500);
            return;
        }

        // si pasa validacion retornamos un json exitoso 
        Response::json([
            "message" => "Pregunta creada",
            "id" => $question
        ], 201);
    }

    public static function update($questionId, $data)
    {
        if (!is_numeric($questionId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        // mandamos a llamar al metodo find para actualizarlo en breve
        $question = Question::find($questionId);

        if (!$question) {
            Response::json([
                "error" => "Pregunta no encontrada"
            ], 404);
            return;
        }

        // mandamos a llamar el metodo uptaded para ahora si actualizarlo
        $updated = Question::update($questionId, $data);

        // si no existe updated entonces decimos que no se pudo actualizar
        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            return;
        }

        // retornamos un mensaje de pregunta actualizada
        Response::json([
            "message" => "Pregunta actualizada"
        ]);
    }

    public static function destroy($questionId)
    {
        if (!is_numeric($questionId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }
        // mandamos a traer la funcion para buscar la pregunta que borraremos
        $question = Question::find($questionId);

        // si question no existe entonces no se pudo encontrar la pagina
        if (!$question) {
            Response::json([
                "error" => "No se pudo encontrar la pregunta"
            ], 404);
            return;
        }

        // mandamos a llamar la funcion delete pero esta vez no lo almacenamos en una variable
        $deleted = Question::delete($questionId);

        if (!$deleted) {
            Response::json([
                "error" => "No se pudo eliminar la pregunta"
            ], 500);
            return;
        }
        Response::json([
            "message" => "Pregunta eliminada"
        ]);
    }
}

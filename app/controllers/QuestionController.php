<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';

class QuestionController
{
    public static function allWithOptionsCount()
    {
        $options = Question::allWithOptionsCount();

        Validator::emptyCollection($options, "Opciones");
        Response::json($options);
    }

    public static function getOptionsByQuestions($questionId)
    {
        Validator::validateId($questionId);
        $OptionsByQuestion = Question::getOptionsByQuestions($questionId);

        Validator::notFound($OptionsByQuestion, "Opcion");
        Response::json($OptionsByQuestion);
    }

    public static function show($questionId)
    {
        Validator::validateId($questionId);

        $question = Question::find($questionId);

        Validator::notFound($question, "Pregunta");

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
            empty($data["pregunta"]) ||
            empty($data["points"])
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
        Validator::validateId($questionId);

        // mandamos a llamar al metodo find para actualizarlo en breve
        $question = Question::find($questionId);

        Validator::notFound($question, "Pregunta");

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
        Validator::validateId($questionId);
        // mandamos a traer la funcion para buscar la pregunta que borraremos
        $question = Question::find($questionId);

        // si question no existe entonces no se pudo encontrar la pagina
        Validator::notFound($question, "Pregunta");

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

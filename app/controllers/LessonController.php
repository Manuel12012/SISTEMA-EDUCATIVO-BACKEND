<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../core/Response.php';

class LessonController
{

    public static function byModule($moduleId)
    {
        Validator::validateId($moduleId);

        $module = Module::find($moduleId);

        Validator::notFound($module, "Modulo");

        $lessons = Lesson::getByModule($moduleId);

        Response::json($lessons);
    }

    public static function index()
    {
        $lesson = Lesson::all();
        Validator::emptyCollection($lesson, "Lecciones");
        Response::json($lesson);
    }

    public static function show($lessonId)
    {
        Validator::validateId($lessonId);
        $lesson = Lesson::find($lessonId);

        Validator::notFound($lesson, "Leccion");

        $module = Module::getByLesson($lessonId);

        if (!$module) {
            Response::json([
                "error" => "Modulo no encontrado"
            ]);
            return;
        }
        Response::json(
            $lesson

        );
    }

    public static function store($data)
    {
        if (
            empty($data["module_id"]) ||
            empty($data["titulo"]) ||
            empty($data["tipo"]) ||
            empty($data["contenido"]) ||
            empty($data["orden"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ], 400);
            return;
        }

        $lesson = Lesson::create($data);

        if (!$lesson) {
            Response::json([
                "error" => "No se pudo crear la leccion"
            ]);
            return;
        }

        Response::json([
            "message" => "Leccion creada",
            "id" => $lesson
        ], 201);
    }
    public static function update($lessonId, $data)
    {
        Validator::validateId($lessonId);

        $lesson = Lesson::find($lessonId);

        Validator::notFound($lesson, "Leccion");

        $updated = Lesson::update($lessonId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            return;
        }
        Response::json([
            "message" => "Leccion actualizada"
        ]);
    }

    public static function destroy($lessonId)
    {
        Validator::validateId($lessonId);

        $lesson = Lesson::find($lessonId);

        Validator::notFound($lesson, "Leccion");
        
        Lesson::delete($lessonId);

        Response::json([
            "message" => "Leccion eliminada"
        ]);
    }
}

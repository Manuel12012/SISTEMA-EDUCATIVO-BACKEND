<?php

require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/Course.php';


class ModuleController
{
    public static function byCourse($courseId)
    {
        if (!is_numeric($courseId)) {
            Response::json([
                "error" => "ID de módulo inválido"
            ], 400);
            return;
        }

        $courses = Course::find($courseId);

        if (!$courses) {
            Response::json([
                "error" => "Curso no encontrado"
            ], 404);
            return;
        }

        $modules = Module::getByCourse($courseId);

        Response::json($modules);
    }

    public static function index()
    {
        $titulo = $_GET["titulo"] ?? null;
        $course_id = $_GET["course_id"]?? null;
    
        if ($titulo && $course_id) {
            $modules = Module::getByTitle($titulo, $course_id);
        } else {
            $modules = Module::all(); 
        }
    
        if (empty($modules)) {
            Response::json($modules);
            return;
        }
    
        Response::json($modules);
    }

    public static function show($moduleId)
    {
        if (!is_numeric(value: $moduleId)) {
            Response::json(
                [
                    "error" => "Id del modulo invalido"
                ]
            );
            return;
        }

        $modules = Module::find($moduleId);

        if (!$modules) {
            Response::json([
                "error" => "Modulo no encontrado"
            ]);
            return;
        }
        Response::json($modules);
    }

    public static function store($data)
    {
        if (
            empty($data["course_id"]) ||
            empty($data["titulo"]) ||
            empty($data["orden"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ], 400);
            return;
        }

        $modules = Module::create($data);

        if (!$modules) {
            Response::json([
                "error" => "No se puedo crear el modulo"
            ]);
            return;
        }

        Response::json([
            "message" => "Modulo creado",
            "id" => $modules
        ], 201);
    }

    public static function update($moduleId, $data)
    {
        if (!is_numeric($moduleId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $modules = Module::find($moduleId);


        if (!$modules) {
            Response::json([
                "error" => "Module no encontrado"
            ], 404);
            return;
        }

        $updated = Module::update($moduleId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            return;
        }
        Response::json([
            "message" => "Modulo actualizado"
        ]);
    }

    public static function destroy($moduleId)
    {
        if (!is_numeric($moduleId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $modules = Module::find($moduleId);

        if (!$modules) {
            Response::json([
                "error" => "No se pudo encontrar el modulo"
            ], 404);
            return;
        }

        $deleted = Module::delete($moduleId);

        if (!$deleted) {
            Response::json([
                "error" => "No se pudo eliminar el modulo"
            ]);
            return;
        }

        Response::json([
            "message" => "Modulo eliminado"
        ]);
    }

}

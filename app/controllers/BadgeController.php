<?php
require_once __DIR__ . '/../models/Badge.php';
require_once __DIR__ . '/../core/Response.php';
class BadgeController
{
    public static function index()
    {
        $badge = Badge::all();

        if (empty($badge)) {
            Response::json([
                "error" => "No se encontro el badge"
            ], 200);
        }
        Response::json($badge);
    }

    public static function show($badgeId)
    {
        if (!is_numeric($badgeId)) {
            Response::json([
                "error" => "Id del badge invalido"
            ], 400);
        }

        $badge = Badge::find($badgeId);

        if (!$badge) {
            Response::json([
                "error" => "Badge no encontrada"
            ], 404);
        }

        Response::json($badge);
    }

    public static function store($data)
    { // $data viene de create donde 
        // contiene los datos de la pregunta

        // si no existen esos campos entonces mandamos un response con datos incompletos
        if (
            empty($data["nombre"]) ||
            empty($data["descripcion"]) ||
            empty($data["icono_url"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ],400);
        }

        // llamamos la funcion create y la almacenamos en question
        $badge = Badge::create($data);

        // si question no existe entonces decimos que no se pudo crear la pregunta
        if (!$badge) {
            Response::json([
                "error" => "No se pudo crear el badge"
            ], 500);
        }

        // si pasa validacion retornamos un json exitoso 
        Response::json([
            "message" => "Badge creada",
            "id" => $badge
        ], 201);


    }
}

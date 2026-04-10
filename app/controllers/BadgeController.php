<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/Badge.php';
require_once __DIR__ . '/../core/Response.php';
class BadgeController
{
    public static function index()
    {
        $badge = Badge::all();
        Validator::emptyCollection($badge, "Badges");
        Response::json($badge);
    }

    public static function show($badgeId)
    {
        Validator::validateId($badgeId);
        $badge = Badge::find($badgeId);

        Validator::notFound($badge, "Badge");
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
        Validator::notFound($badge, "Badge");

        // si pasa validacion retornamos un json exitoso 
        Response::json([
            "message" => "Badge creada",
            "id" => $badge
        ], 201);


    }
}

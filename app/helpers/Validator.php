<?php

namespace App\helpers;

use Response;

class Validator
{

    public static function validateId($id)
    {
        if (!is_numeric($id)) {
            Response::json(["error" => "ID inválido"], 400);
            exit;
        }
    }

    public static function notFound($item, $entity)
    {
        if (!$item) {
            Response::json([
                "error" => "$entity no encontrado"
            ], 404);
            exit;
        }
    }

    public static function emptyCollection($items, $entity)
    {
        if (empty($items)) {
            Response::json([
                "error" => "No hay $entity disponibles"
            ], 404);
            exit;
        }
    }
}

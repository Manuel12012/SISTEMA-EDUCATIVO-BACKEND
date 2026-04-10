<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/PointHistory.php';


class PointHistoryController
{
    public static function byUser($userId)
    {
        Validator::validateId($userId);
        // Verificar que el usuario exista
        $user = User::find($userId);

        Validator::notFound($user, "Usuario");

        // Obtener historial de puntos del usuario
        $histories = PointsHistory::getByUser($userId);

        Response::json([
            "user_id" => $userId,
            "point_histories" => $histories
        ]);
    }


    public static function index()
    {
        $histories = PointsHistory::all();
        Validator::emptyCollection($histories, "Historiales");
        Response::json($histories);
    }

    public static function show($pointHistoryId)
    {
        Validator::validateId($pointHistoryId);
        $histories = PointsHistory::find($pointHistoryId);

        Validator::notFound($histories, "Historial");
        Response::json([
            "pointHistory" => $histories
        ]);
    }

    public static function update($pointHistoryId, $data)
    {
        Validator::validateId($pointHistoryId);
        $histories = PointsHistory::find($pointHistoryId);

        Validator::notFound($histories, "Historial");
        $updated = PointsHistory::update($pointHistoryId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            return;
        }
        Response::json([
            "message" => "Historial de puntos actualizado"
        ]);
    }

    public static function destroy($pointHistoryId)
    {
        Validator::validateId($pointHistoryId);
        $histories = PointsHistory::find($pointHistoryId);

        Validator::notFound($histories, "Historial");
        $deleted = PointsHistory::delete($pointHistoryId);

        if (!$deleted) {
            Response::json([
                "error" => "No se pudo borrar el historial"
            ]);
            return;
        }

        Response::json([
            "message" => "Historial eliminado"
        ]);
    }
}

<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ .'/../core/Response.php';
require_once __DIR__ .'/../models/PointHistory.php';


class PointHistoryController
{public static function byUser($userId)
{
    // Validar ID
    if (!is_numeric($userId)) {
        Response::json([
            "error" => "ID de usuario inválido"
        ], 400);
    return;
    }

    // Verificar que el usuario exista
    $user = User::find($userId);

    if (!$user) {
        Response::json([
            "error" => "Usuario no encontrado"
        ], 404);
    return;
    }

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

        if (empty($histories)) {
            Response::json(
                [
                    "error" => "No se encontro el historial de puntos"
                ],404
            );
                return;

        }
        Response::json($histories);
    }

    public static function show($pointHistoryId)
    {
        if (!is_numeric($pointHistoryId)) {
            Response::json(
                [
                    "error" => "Id del historial de puntos no valido"
                ]
            );
            return;
        }

        $histories = PointsHistory::find($pointHistoryId);

        if (!$histories) {
            Response::json([
                "error" => "Historial de puntos no encontrado"
            ]);
            return;
        }

        Response::json([
            "pointHistory" => $histories
        ]);
    }

    public static function update($pointHistoryId, $data)
    {
        if (!is_numeric($pointHistoryId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $histories = PointsHistory::find($pointHistoryId);

        if (!$histories) {
            Response::json([
                "error" => "Historial no encontrado"
            ], 404);
            return;
        }

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
        if (!is_numeric($pointHistoryId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $histories = PointsHistory::find($pointHistoryId);

        if (!$histories) {
            Response::json([
                "error" => "No se pudo encontrar el historial"
            ], 404);
            return;
        }
        $deleted = PointsHistory::delete($pointHistoryId);

        if(!$deleted){
            Response::json([
                "error"=> "No se pudo borrar el historial"
            ]);
            return;
        }

        Response::json([
            "message" => "Historial eliminado"
        ]);
    }
}

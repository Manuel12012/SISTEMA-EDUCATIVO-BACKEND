<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/UserBadge.php';
require_once __DIR__ . '/../core/Response.php';


class UserBadgeController
{
    public static function indexByUser($userId)
    {
        if (!is_numeric($userId)) {
            Response::json([
                "error" => "Id de usuario inválido"
            ], 400);
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            Response::json([
                "error" => "Usuario no encontrado"
            ], 404);
            return;
        }

        $badges = UserBadge::getBadgesByUser((int)$userId);

        Response::json([
            "user_id" => $userId,
            "badges" => $badges
        ]);
    }


    public static function store($userId, $badgeId)
    {
        if (!is_numeric($userId) || !is_numeric($badgeId)) {
            Response::json([
                "error" => "Datos inválidos"
            ]);
            return;
        }

        $assigned = UserBadge::assign((int)$userId, (int)$badgeId);

        if (!$assigned) {
            Response::json([
                "error" => "El usuario ya tiene este badge"
            ], 409);
            return;
        }

        Response::json([
            "message" => "Badge asignado correctamente"
        ], 201);
    }
    public static function destroy($userId, $badgeId)
    {
        if (!is_numeric($userId) || !is_numeric($badgeId)) {
            Response::json([
                "error" => "Datos inválidos"
            ]);
            return;
        }

        $removed = UserBadge::removeBadge((int)$userId, (int)$badgeId);

        if (!$removed) {
            Response::json([
                "error" => "No se pudo eliminar el badge"
            ]);
            return;
        }

        Response::json([
            "message" => "Badge eliminado correctamente"
        ]);
    }
}

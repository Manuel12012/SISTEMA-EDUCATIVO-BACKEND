<?php

namespace App\Middleware;

class RoleMiddleware
{
    public static function handle($user, $roles = [])
    {
        if (!in_array($user->rol, $roles)) {
            http_response_code(403);
            echo json_encode([
                "error" => "No tienes permisos para acceder"
            ]);
            exit;
        }
    }
}
<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public static function verify()
    {
        // extraemos todos los headers
        $headers = getallheaders();

        // si headers no tiene authorization entonces mostramos error
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(["error" => "Token requerido"]);
            exit;
        }

        // guardamos el token en authHeader
        $authHeader = $headers['Authorization'];

        // verificamos que tenga el formato correcto
        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["error" => "Formato de token inválido"]);
            exit;
        }
        // guardamos el  token en jwt
        $jwt = $matches[1];

        // retornamos el token si nada falla
        try {
            $decoded = JWT::decode($jwt, new Key(JWT_SECRET, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            // si falla mandamos error
            http_response_code(401);
            echo json_encode(["error" => "Token inválido o expirado"]);
            exit;
        }
    }

    public static function getUserId()
    {
        $decoded = AuthMiddleware::verify();
        return $decoded->id ?? null;
    }
}

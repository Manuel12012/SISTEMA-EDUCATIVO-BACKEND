<?php
require_once __DIR__ . '/../models/Question.php';
require_once __DIR__ . '/../models/ExamOption.php';
require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../models/User.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Middleware\AuthMiddleware;

class UserController
{
    public static function resultsByUser($userId)
    { // obtener resultados del examen a partir del usuario
        if (!is_numeric($userId)) {
            Response::json(
                [
                    "error" => "ID no encontrado"
                ]
            );
            return;
        }

        $result = ExamResult::getByUser($userId);

        if (!$result) {
            Response::json(
                [
                    "error" => "No se pudo obtener el resultado del examen"
                ]
            );

            exit;
        }

        Response::json($result);
    }

    public static function index()
    {
        $page = $_GET["page"] ?? 1;
        $limit = $_GET["limit"] ?? 10;
        $rol = $_GET["role"] ?? null;
    
        // 🔥 SI VIENE FILTRO
        if ($rol) {
            $users = User::getByRol($rol, $page, $limit);
        } else {
            $users = User::paginate($page, $limit);
        }
    
        if (empty($users)) {
            Response::json([
                "error" => "No se encontraron usuarios"
            ]);
            exit;
        }
    
        Response::json($users);
    }

    public static function show($userId)
    {
        if (!is_numeric($userId)) {
            Response::json(
                [
                    "error" => "Id del usuario no encontrado"
                ]
            );

            exit;
        }

        $user = User::find($userId);

        if (!$user) {
            Response::json([
                "error" => "Usuario no encontrado"
            ]);

            exit;
        }

        Response::json($user);
    }

    public static function store($data)
    {
        $required = [
            "nombre",
            "email",
            "password",
            "rol",
            "avatar_url"
        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                Response::json([
                    "error" => "Falta el campo: $field"
                ], 400);
                exit;
            }
        }

        $user = User::create($data);

        if (!$user) {
            Response::json([
                "error" => "No se pudo crear el usuario"
            ], 500);
            exit;
        }

        Response::json([
            "message" => "Usuario creado",
            "id" => $user
        ], 201);
    }
    public static function update($userId, $data)
    {
        if (!is_numeric($userId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            exit;
        }

        $user = User::find($userId);


        if (!$user) {
            Response::json([
                "error" => "Usuario no encontrado"
            ], 404);
            exit;
        }

        $updated = User::update($userId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            exit;
        }
        Response::json([
            "message" => "Usuario actualizado"
        ]);
    }

    public static function destroy($userId)
    {
        if (!is_numeric($userId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            return;
        }

        $user = User::find($userId);

        if (!$user) {
            Response::json([
                "error" => "No se pudo encontrar el usuario"
            ], 404);
            exit;
        }
        User::delete($userId);

        Response::json([
            "message" => "Usuario eliminado"
        ]);
    }

    public static function login($data)
    {
        // verificamos si email y password existen
        if (empty($data["email"]) || empty($data["password"])) {
            Response::json([
                "error" => "Password y email son obligatorios"
            ], 400);
            exit;
        }
        // buscamos usuario por email
        $user = User::findByEmail($data["email"]);

        //si usuario no existe mostramos
        if (!$user) {
            Response::json([
                "error" => "Usuario no encontrado"
            ], 404);
            exit;
        }

        //si el password es incorrecto
        if (!password_verify($data["password"], $user["password"])) {
            Response::json([
                "error" => "Credenciales incorrectas"
            ], 401);
            exit;
        }

        $payload = [
            "id" => $user["id"],
            "email" => $user["email"],
            "rol" => $user["rol"],
            "exp" => time() + (60 * 60) // 1 hora
        ];

        $jwt = JWT::encode($payload, JWT_SECRET, 'HS256');
        // quitamos el password de user para no mostrarlo en el backend
        unset($user["password"]);

        Response::json([
            "message" => "Login exitoso",
            "token" => $jwt,
            "user" => [
                "id" => $user["id"],
                "email" => $user["email"],
                "rol" => $user["rol"]
            ]
        ], 200);
    }

    public static function me()
    {
        $user = AuthMiddleware::verify();

        Response::json([
            "user" => [
                "id" => $user->id,
                "email" => $user->email,
                "rol" => $user->rol
            ]
        ], 200);
    }
}

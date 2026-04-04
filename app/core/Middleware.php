<?php

use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class Middleware
{
    public static function resolve($name)
    {
        // por cada nombre del case 
        switch ($name) {

        // para el caso de auth, que es el autenticador con jwt si existe ese usuario
            case "auth":
                // retornamos la funcion de verificado del authMiddleware
                return function () {
                    AuthMiddleware::verify();
                };

                // para el caso de admin
            case "admin":

                // retornamos el usuario verificado y el rol de admin
                return function () {
                    $user = AuthMiddleware::verify();
                    RoleMiddleware::handle($user, ["admin"]);
                };

        }

        throw new Exception("Middleware $name not found");
    }
}
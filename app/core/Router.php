<?php

class Router
{
    //guardamos las rutas registradas
    private static array $routes = [];

    // creamos la variable para almacenar los grupos de middlewares
    public static $groupMiddlewares = [];

    // creamos el metodo middlewares para manejar grupos
    public static function middleware($middlewares)
    {
        // ahora el grupo de middlewares sera igual a middlwares
        self::$groupMiddlewares = $middlewares;

        // retornamos una clase anónima para poder encadenar ->group()
        return new class {

            // la funcion para agruptar y recibe un callback
            public function group($callback)
            {
                // ejecutamos el call back
                $callback();

                // el grupo comenzara como un array vacio
                Router::$groupMiddlewares = [];
            }
        };
    }


    // metodos publicos GET POST PUT DELETE
    public static function get($uri, $action, $middlewares = [])
    {
        self::add('GET', $uri, $action, $middlewares);
    }

    public static function post($uri, $action, $middlewares = [])
    {
        self::add('POST', $uri, $action, $middlewares);
    }

    public static function put($uri, $action, $middlewares = [])
    {
        self::add('PUT', $uri, $action, $middlewares);
    }

    public static function delete($uri, $action, $middlewares = [])
    {
        self::add('DELETE', $uri, $action, $middlewares);
    }

    // guarda la ruta en el array, limpia el / 
    private static function add($method, $uri, $action, $middlewares = [])
    {
        // unimos los valores de ambos middlewares
        $middlewares = array_merge(self::$groupMiddlewares, $middlewares);
        self::$routes[] = [
            'method' => $method,
            'uri' => trim($uri, '/'),
            'action' => $action,
            'middlewares' => $middlewares
        ];
    }

    public static function dispatch()
    { // funcion para obtener el request actual
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // quitamos "api/" del inicio
        if (str_starts_with($uri, 'api/')) {
            $uri = substr($uri, 4);
        }
        // recorre todas las rutas registradas
        foreach (self::$routes as $route) {

            // convertimos /questions/{id} → regex
            $pattern = preg_replace('#\{[a-zA-Z]+\}#', '([0-9]+)', $route['uri']);
            $pattern = "#^$pattern$#";

            // si concide el metodo y el uri
            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                // extraemos parametros  id    
                array_shift($matches);

                // Recorremos cada valor del middleware
                foreach ($route["middlewares"] as $middleware) {
                    
                    // si es un string entonces el midleware llamara al metodo resolve de Middleware
                    if (is_string($middleware)) {
                        $middleware = Middleware::resolve($middleware);
                    }

                    // ejecutamos middleware de nuevo? como funcion?
                    $middleware();
                }

                // resuelve el controller + metodo
                [$controller, $methodName] = $route['action'];
                // leer body según Content-Type
                $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

                if (str_contains($contentType, 'multipart/form-data')) {
                    // para uploads, los datos vienen en $_POST y $_FILES
                    $body = $_POST;
                } else {
                    // para JSON normal
                    $body = json_decode(file_get_contents("php://input"), true) ?? [];
                }

                // llamar al controller
                call_user_func_array(
                    [$controller, $methodName],
                    array_merge($matches, [$body])
                );
                return;
            }
        }

        Response::json([
            "error" => "Ruta no encontrada"
        ], 404);
    }
}

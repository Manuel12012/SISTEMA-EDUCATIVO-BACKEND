<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../core/Response.php';

class CourseController
{

    public static function index()
    {
        $titulo = $_GET["titulo"] ?? null;

        if($titulo){
            $courses = Course::getByTitle($titulo);
        } else{
            $courses = Course::all();
        }  

        // validacion si existe el curso o no
        if (empty($courses)) {
            Response::json(["error" => "No se encontraron cursos"], 404);
            return;
        }

        // devolvemos un json pasandole la variable courses
        Response::json($courses);
    }

    public static function show($id)
    {
        // validamos con la funcion is numeric si es un valor numero
        if (!is_numeric($id)) {
            // devolvemos un json que es llamado desde Response.php con el metodo json
            Response::json([
                "error" => "ID de curso invalido"
            ], 400);
            return;
        }
        // en la variable course usamos find y le pasamos el id como parametro
        $course = Course::find((int) $id);
        // si curso no existe entonces imprimira curso no encontrado
        if (!$course) {
            Response::json(
                [
                    "error" => "Curso no encontrado"
                ],
                404
            );
            exit;
        }

        Response::json($course);
    }

    public static function store($data)
    {
         $data = json_decode(file_get_contents("php://input"), true);
        if (
            empty($data["titulo"]) ||
            empty($data["descripcion"]) ||
            empty($data["grado"]) ||
            empty($data["color"])
        ) {
            Response::json([
                "error" => "Datos incompletos"
            ], 400);
            exit;
        }

        $course = Course::create($data);

        if (!$course) {
            Response::json([
                "error" => "No se pudo crear el curso"
            ], 500);
            exit;
        }

        Response::json([
            "message" => "Curso creado",
            "id" => $course
        ], 201);
    }

    public static function update($courseId, $data)
    {
        if (!is_numeric($courseId)) {
            Response::json([
                "error" => "ID invalido"
            ], 400);
            return;
        }

        $course = Course::find($courseId);

        if (!$course) {
            Response::json([
                "error" => "No se encontro el curso"
            ]);
            exit;
        }

        $updated = Course::update($courseId, $data);

        if (!$updated) {
            Response::json([
                "error" => "No se pudo actualizar"
            ], 500);
            exit;
        }

        Response::json([
            "message" => "Curso actualizado"
        ]);
    }

    public static function destroy($courseId)
    {
        if (!is_numeric($courseId)) {
            Response::json(
                [
                    "error" => "ID invalido"
                ],
                400
            );
            exit;
        }

        $course = Course::find($courseId);

        if (!$course) {
            Response::json([
                "error" => "No se pudo encontrar el curso"
            ], 404);
            exit;
        }

        Course::delete($courseId);

        Response::json([
            "message" => "Curso eliminado"
        ]);
    }

    public static function modules($courseId)
    { // usamos esta funcion para traernos modulos mediante el id del curso
        if (!is_numeric($courseId)) {
            Response::json(
                [
                    "error" => "ID de curso invalido"
                ],
                400
            );
            exit;
        }
        $course = Course::find((int) $courseId);

        // si no existe course entonces mostramos curso no encontrado
        if (!$course) {
            Response::json([
                "error" => "Curso no encontrado"
            ], 404);
            exit;
        }
        // usamos getByCourse del modelo Module y le pasamos courseId y lo almacenamos en $modules
        $modules = Module::getByCourse((int)$courseId);

        Response::json($modules);
    }


    public static function allWithModulesCount()
    {
        $courses = Course::allWithCourseCount();

        if (empty($courses)) {
            Response::json(["error" => "No se encontro el curso", 404]);
            return;
        }
        Response::json($courses);

        
    }

    public static function uploadImage()
{
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file = $_FILES['image'];

        // Crear carpeta si no existe
        $uploadDir = __DIR__ . '/../../public/uploads/courses/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Crear nombre único siempre en .jpg
        $filename = time() . '_' . pathinfo($file['name'], PATHINFO_FILENAME) . '.jpg';
        $destination = $uploadDir . $filename;

        // Detectar tipo y convertir a JPG con GD
$mime = $file['type'];
        $image = match($mime) {
            'image/jpeg' => imagecreatefromjpeg($file['tmp_name']),
            'image/png'  => imagecreatefrompng($file['tmp_name']),
            'image/gif'  => imagecreatefromgif($file['tmp_name']),
            'image/webp' => imagecreatefromwebp($file['tmp_name']),
            default      => null
        };

        if (!$image) {
            Response::json(['error' => 'Formato no soportado']);
            return;
        }

        // Guardar siempre como JPG
        if (imagejpeg($image, $destination, 90)) {
            imagedestroy($image); // liberar memoria
            $url = '/uploads/courses/' . $filename;
            Response::json(['imageUrl' => $url]);
        } else {
            Response::json(['error' => 'Error al guardar la imagen']);
        }

    } else {
        Response::json(['error' => 'No se envió archivo']);
    }
}
}
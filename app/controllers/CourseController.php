<?php

use App\helpers\Validator;

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../core/Response.php';

class CourseController
{

    public static function index()
    {
        $titulo = $_GET["titulo"] ?? null;

        $courses = $titulo ? Course::getByTitle($titulo) : Course::all();
        // validacion si existe el curso o no
        Validator::emptyCollection($courses, "Cursos");
        // devolvemos un json pasandole la variable courses
        Response::json($courses);
    }

    public static function show($id)
    {
        Validator::validateId($id);
        // en la variable course usamos find y le pasamos el id como parametro
        $course = Course::find((int) $id);
        // si curso no existe entonces imprimira curso no encontrado
        Validator::notFound($course, "Curso");

        Response::json($course);
    }
    public static function store()
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

        Response::json([
            "message" => "Curso creado",
            "id" => $course
        ], 201);
    }

    public static function update($courseId, $data)
    {
        Validator::validateId($courseId);

        $course = Course::find($courseId);

        Validator::notFound($course, "Curso");

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
        Validator::validateId($courseId);
        $course = Course::find($courseId);

        Validator::notFound($course, "Curso");
        Course::delete($courseId);

        Response::json([
            "message" => "Curso eliminado"
        ]);
    }

    public static function modules($courseId)
    { // usamos esta funcion para traernos modulos mediante el id del curso
        Validator::validateId($courseId);
        $course = Course::find((int) $courseId);

        // si no existe course entonces mostramos curso no encontrado
        Validator::notFound($course, "Curso");
        // usamos getByCourse del modelo Module y le pasamos courseId y lo almacenamos en $modules
        $modules = Module::getByCourse((int)$courseId);

        Response::json($modules);
    }


    public static function allWithModulesCount()
    {
        $courses = Course::allWithCourseCount();
        Validator::emptyCollection($courses, "Cursos");
        Response::json($courses);
    }

    public static function uploadImage()
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file = $_FILES['image'];

            // Crear carpeta si no existe
            $uploadDir = __DIR__ . "/../../public/uploads/courses/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($file['type'], $allowed)) {
                Response::json(["error" => "Tipo de archivo no permitido"]);
                return;
            }
            // Crear nombre único siempre en .jpg
            $filename = uniqid() . '.jpg';
            $destination = $uploadDir . $filename;

            // Detectar tipo y convertir a JPG con GD
            $mime = $file['type'];
            $image = match ($mime) {
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

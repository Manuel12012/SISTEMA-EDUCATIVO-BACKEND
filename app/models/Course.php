<?php

require_once __DIR__ . '/../core/Model.php';

class Course extends Model
{
    protected static string $table = 'courses';

    public static function all()
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM "
                . self::$table
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $courseId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM courses WHERE id = :id"
        );
        $stmt->execute(["id" => $courseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO courses (titulo, descripcion, grado, imagen_url, color)
                VALUES (:titulo, :descripcion, :grado, :imagen_url, :color)"
        );
        $stmt->execute([
            "titulo" => $data["titulo"],
            "descripcion" => $data["descripcion"],
            "grado" => $data["grado"],
            "color" => $data["color"],
            "imagen_url" => $data["imagen_url"] ?? "",
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update($courseId, $data)
    { // creamos dos variables questionId y data
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE courses SET titulo = :titulo, descripcion = :descripcion,
                grado = :grado, imagen_url = :imagen_url, color = :color
            WHERE id = :id"
        ); // retornamos igual un stmt y lo almacenamos en un array $data y tambien el $questionId
        return $stmt->execute([
            "titulo" => $data["titulo"],
            "descripcion" => $data["descripcion"],
            "grado" => $data["grado"],
            "imagen_url" => $data["imagen_url"] ?? "",
            "color" => $data["color"],
            "id" => $courseId
        ]);
    }

    public static function delete($courseId)
    { // le pasamos como parametro questionId
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM courses WHERE id = :id"
        );
        // no devolveremos nada, ya que se borro la pregunta
        return $stmt->execute(["id" => $courseId]);
    }

    public static function allWithCourseCount()
    {
        $db = Database::connect();

        $sql = "    
        SELECT
            c.id,
            c.titulo,
            c.descripcion,
            c.grado,
            c.imagen_url,
            c.color,
            COUNT(m.id) AS modules_count
            FROM courses c
            LEFT JOIN modules m ON m.course_id = c.id
            GROUP BY
            c.id,
            c.titulo,
            c.descripcion,
            c.grado,
            c.imagen_url,
            c.color
        ";
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByTitle($titulo)
    {
        $db = Database::connect();

        $stmt = $db->prepare(
            "SELECT * FROM courses WHERE titulo LIKE :titulo"
        );

        $stmt->bindValue(":titulo", "%" . $titulo . "%");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

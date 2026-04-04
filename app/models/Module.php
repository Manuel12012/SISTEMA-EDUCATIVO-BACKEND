<?php

require_once __DIR__ . '/../core/Model.php';

class Module extends Model
{
    protected static string $table = 'modules';

    public static function getByCourse(int $courseId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM modules WHERE course_id = :id"
        );
        $stmt->execute(['id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByLesson(int $lessonId)
    {
        $db = Database::connect();
        $stmt = $db->prepare("
        SELECT m.*
        FROM modules m
        JOIN lessons l ON l.module_id = m.id
        WHERE l.id = :id
    ");
        $stmt->execute(['id' => $lessonId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function find(int $moduleId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM modules WHERE id = :id"
        );
        $stmt->execute(["id" => $moduleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    { // $data contiene los datos de la pregunta a insertar
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO modules (course_id, titulo, orden)
                    VALUES (:course_id, :titulo, :orden)"
        );
        $stmt->execute([
            "course_id" => $data["course_id"],
            "titulo" => $data["titulo"],
            "orden" => $data["orden"],
        ]);
        // retornamos con lastInsertId porque sera de manera auto_increment
        return (int) $db->lastInsertId();
    }

    public static function update($moduleId, $data)
    { // creamos dos variables questionId y data
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE modules SET course_id = :course_id, titulo = :titulo,
                orden = :orden
            WHERE id = :id"
        ); // retornamos igual un stmt y lo almacenamos en un array $data y tambien el $questionId
        return $stmt->execute([
            "course_id" => $data["course_id"],
            "titulo" => $data["titulo"],
            "orden" => $data["orden"],
            "id" => $moduleId
        ]);
    }

    public static function delete($moduleId)
    { // le pasamos como parametro questionId
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM modules WHERE id = :id"
        );
        // no devolveremos nada, ya que se borro la pregunta
        return $stmt->execute(["id" => $moduleId]);
    }

    public static function getByTitle($titulo, $course_id) {

        $db = Database::connect();
    
        $stmt = $db->prepare(
            "SELECT * FROM modules WHERE titulo LIKE :titulo 
            AND course_id = :course_id"
        );
    
        $stmt->bindValue(":titulo", "%" . $titulo . "%");
        $stmt->bindValue(":course_id",  $course_id );

    
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php

require_once __DIR__ . "/../core/Model.php";

class Lesson extends Model
{
    protected static string $table = 'lessons';

    public static function getByModule(int $moduleId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM lessons WHERE module_id = :id ORDER BY orden"
        );
        $stmt->execute(['id' => $moduleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $lessonId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM lessons WHERE id = :id"
        );
        $stmt->execute(['id' => $lessonId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    { // $data contiene los datos de la pregunta a insertar
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO lessons (module_id, titulo, tipo, contenido, orden
        ) VALUES (:module_id, :titulo, :tipo, :contenido, :orden)"
        );
        $stmt->execute([
            "module_id" => $data["module_id"],
            "titulo" => $data["titulo"],
            "tipo" => $data["tipo"],
            "contenido" => $data["contenido"],
            "orden" => $data["orden"],
        ]);
        // retornamos con lastInsertId porque sera de manera auto_increment
        return (int) $db->lastInsertId();
    }

    public static function update($lessonId, $data)
    { // creamos dos variables questionId y data
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE lessons SET module_id = :module_id, titulo = :titulo,
                tipo = :tipo, contenido = :contenido, orden = :orden WHERE id = :id"
        ); // retornamos igual un stmt y lo almacenamos en un array $data y tambien el $questionId
        return $stmt->execute([
            "module_id" => $data["module_id"],
            "titulo" => $data["titulo"],
            "tipo" => $data["tipo"],
            "contenido" => $data["contenido"],
            "orden" => $data["orden"],
            "id" => $lessonId
        ]);
    }

    public static function delete($lessonId)
    { // le pasamos como parametro questionId
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM lessons WHERE id = :id"
        );
        // no devolveremos nada, ya que se borro la pregunta
        return $stmt->execute(["id" => $lessonId]);
    }
}

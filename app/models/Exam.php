<?php

require_once __DIR__ . '/../core/Model.php';

class Exam extends Model
{
    protected static string $table = 'exams';


    public static function getWithQuestions($examId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT e.*, q.id AS question_id, q.pregunta FROM exams e
                INNER JOIN questions q ON q.exam_id = e.id
                WHERE e.id = :id"
        );
        $stmt->execute(['id' => $examId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getQuestionsByExam($examId)
    {
        $db = Database::connect();

        $sql = "
        SELECT 
            q.id,
            q.exam_id,
            q.pregunta,
            q.correct_option_id,
            COUNT(eo.id) AS option_count
        FROM questions q
        LEFT JOIN exam_options eo 
            ON eo.question_id = q.id
        WHERE q.exam_id = :exam_id
        GROUP BY q.id, q.exam_id, q.pregunta, q.correct_option_id
    ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['exam_id' => $examId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function find(int $examId): ?array
    {
        $db = Database::connect();

        $sql = "
SELECT 
        e.*,
        c.titulo AS course_titulo,
        (
            SELECT COUNT(*) 
            FROM questions q 
            WHERE q.exam_id = e.id
        ) AS question_count
    FROM exams e
    LEFT JOIN courses c
        ON c.id = e.course_id
    WHERE e.id = :id
    ";

        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $examId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }


    public static function create($data)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO exams (course_id, titulo, duracion_minutos, created_by) VALUES
            (:course_id, :titulo, :duracion_minutos, :created_by)"
        );
        $stmt->execute([
            "course_id" => $data["course_id"],
            "titulo" => $data["titulo"],
            "duracion_minutos" => $data["duracion_minutos"],
            "created_by" => $data["created_by"] // viene del controller
        ]);

        return (int) $db->lastInsertId();
    }

    public static function update($examId, $data)
    {
        $db = Database::connect();
        // creamos primero array vacio fields
        $fields = [];
        // array dpmde id es igual a examId
        $params = ["id" => $examId]; //5

        // recorremos data como clave valor
        foreach ($data as $key => $value) { // en el frontend primero se guarda el key y luego el valor
            // key que es titulo sera igual a titulo
            $fields[] = "$key = :$key";
            $params[$key] = $value; //
        }

        $sql = "UPDATE exams SET " . implode(", ", $fields) . " WHERE id = :id";

        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }


    public static function delete($examId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM exams WHERE id = :id"
        );
        // no devolveremos nada, ya que se borro la pregunta
        return $stmt->execute(["id" => $examId]);
    }

    public static function allWithQuestionCount()
    {
        $db = Database::connect();

        $sql = "
SELECT 
    e.id,
    e.course_id,
    c.titulo AS course_titulo,
    e.titulo,
    e.duracion_minutos,
    e.created_at,
    e.created_by,
    e.activo,
    COUNT(q.id) AS questions_count
FROM exams e
LEFT JOIN courses c ON c.id = e.course_id
LEFT JOIN questions q ON q.exam_id = e.id
GROUP BY 
    e.id,
    c.titulo,
    e.titulo,
    e.duracion_minutos,
    e.created_at,
    e.created_by,
    e.activo;

    ";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findActive(int $examId): ?array
    {
        $db = Database::connect();

        $stmt = $db->prepare("
        SELECT id, titulo, duracion_minutos
        FROM exams
        WHERE id = :id
        AND activo = 1
    ");

        $stmt->execute(["id" => $examId]);

        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        return $exam ?: null;
    }

    public static function getQuestionsForTake(int $examId): array
    {
        $db = Database::connect();

        $stmt = $db->prepare("
        SELECT id, pregunta
        FROM questions
        WHERE exam_id = :exam_id
    ");

        $stmt->execute(["exam_id" => $examId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getExamByCourse($courseId)
    {
        $db = Database::connect();

        $stmt = $db->prepare(
            "
        SELECT e.*
        FROM exams e
        JOIN courses c ON c.id = e.course_id
        WHERE c.id = :id"
        );
        $stmt->execute(["id" => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getByTitle($titulo)
    {
        $db = Database::connect();

        $stmt = $db->prepare(
            "SELECT * FROM exams WHERE titulo LIKE :titulo"
        );

        $stmt->bindValue(":titulo", "%" . $titulo . "%");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

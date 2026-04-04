<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/examAnswer.php';
require_once __DIR__ . '/Exam.php';
require_once __DIR__ . '/Question.php';
class ExamResult extends Model
{
    protected static string $table = 'exam_results';

    public static function getByUser($userId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM exam_results WHERE user_id = :id"
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByExam(int $examId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM exam_results WHERE exam_id = :id"
        );
        $stmt->execute(['id' => $examId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByUserAndExam($userId, $examId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM exam_results 
         WHERE user_id = :user 
         AND exam_id = :exam
         LIMIT 1"
        );
        $stmt->execute([
            'user' => $userId,
            'exam' => $examId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function find(int $examResultsId): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT * FROM exam_results WHERE id = :id"
        );
        $stmt->execute(['id' => $examResultsId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create($data)
    { // $data contiene los datos de la pregunta a insertar
        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO exam_results (puntaje, total_preguntas, correctas, duracion_usada,
            completado_en) VALUES (:puntaje, :total_preguntas, :correctas, :duracion_usada, :completado_en )"
        );
        $stmt->execute([
            "puntaje" => $data["puntaje"],
            "total_preguntas" => $data["total_preguntas"],
            "correctas" => $data["correctas"],
            "duracion_usada" => $data["duracion_usada"],
            "completado_en" => $data["completado_en"],
        ]);
        // retornamos con lastInsertId porque sera de manera auto_increment
        return (int) $db->lastInsertId();
    }


    public static function update($examResultsId, $data)
    { // creamos dos variables questionId y data
        $db = Database::connect();
        $stmt = $db->prepare(
            "UPDATE exam_results SET puntaje = :puntaje, total_preguntas = :total_preguntas,
                correctas = :correctas, duracion_usada = :duracion_usada, completado_en = :completado_en
            WHERE id = :id"
        ); // retornamos igual un stmt y lo almacenamos en un array $data y tambien el $questionId
        return $stmt->execute([
            "puntaje" => $data["puntaje"],
            "total_preguntas" => $data["total_preguntas"],
            "correctas" => $data["correctas"],
            "duracion_usada" => $data["duracion_usada"],
            "completado_en" => $data["completado_en"],
            "id" => $examResultsId
        ]);
    }


    public static function delete($examResultsId)
    { // le pasamos como parametro questionId
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM exam_results WHERE id = :id"
        );
        // no devolveremos nada, ya que se borro la pregunta
        return $stmt->execute(["id" => $examResultsId]);
    }

    // creamos una funcion para crear un examen desde los submissions
    public static function createFromSubmission(int $examId, int $userId, array $answers)
    { // conectamos la base de datos e iniciamos beginTransaction 
        $db = Database::connect();
        $db->beginTransaction();


        try {
            // buscamos el examen por el id, y guardamos en $exam
            $exam = Exam::find($examId);

            // si el examen no existe entonces examen no encontrado
            if (!$exam) {
                throw new Exception("Examen no encontrado");
            }
            // Verificar si el usuario ya rindió este examen
            $stmt = $db->prepare("
    SELECT id FROM exam_results
    WHERE user_id = :user_id
    AND exam_id = :exam_id
    LIMIT 1
");

            $stmt->execute([
                "user_id" => $userId,
                "exam_id" => $examId
            ]);

            $alreadyTaken = $stmt->fetch();

            if ($alreadyTaken) {
                throw new Exception("Ya has rendido este examen");
            }
            // obtenemos las preguntas por el examen, y guardamos en $questions
            $questions = Question::getByExam($examId);

            // si questions esta vacio, entonces el examen no tiene preguntas
            if (empty($questions)) {
                throw new Exception("El examen no tiene preguntas");
            }
            $total = count($questions);

            if ($total === 0) {
                throw new Exception("No hay preguntas válidas con respuesta correcta definida.");
            }

            // inicializamos correctas en 0
            $correctas = 0;

            // Crear resultado (iniciamos en 0 el puntaje, correctas y duracion_usada)
            $stmt = $db->prepare("
            INSERT INTO exam_results
            (user_id, exam_id, puntaje, total_preguntas, correctas, duracion_usada)
            VALUES (:user_id, :exam_id, 0, :total, 0, 0)
        ");

            // decimos que cada atributo sera igual a la variable correspondiente $
            $stmt->execute([
                "user_id" => $userId,
                "exam_id" => $examId,
                "total" => $total
            ]);

            // devolvemos un id y almacenamos en resultId
            $resultId = (int) $db->lastInsertId();
            foreach ($questions as $question) {

                $questionId = $question["id"];
                $selectedOptionId = $answers[$questionId] ?? null;

                if (!$selectedOptionId) {
                    continue; // no respondió
                }

                // Buscar opción válida y verificar si es correcta
                $stmtOption = $db->prepare("
        SELECT es_correcta 
        FROM exam_options 
        WHERE id = :id AND question_id = :question_id
    ");

                $stmtOption->execute([
                    "id" => $selectedOptionId,
                    "question_id" => $questionId
                ]);

                $option = $stmtOption->fetch();

                if (!$option) {
                    continue; // opción manipulada o inválida
                }

                $isCorrect = (int) $option["es_correcta"];

                if ($isCorrect === 1) {
                    $correctas++;
                }

                // Guardar respuesta
                ExamAnswer::create([
                    "exam_result_id" => $resultId,
                    "question_id" => $questionId,
                    "selected_option_id" => $selectedOptionId,
                    "es_correcta" => $isCorrect
                ], $db);
            }

            // el puntaje y puntos ganados dependen de esta formula el MAXIMO PUNTAJE SERA 100
            $puntaje = ($correctas / $total) * 100;
            $puntosGanados = $correctas * 10;

            // Actualizar resultado final con los datos anteriores (puntaje y puntos ganados)
            $stmt = $db->prepare("
            UPDATE exam_results
            SET puntaje = :puntaje,
                correctas = :correctas
            WHERE id = :id
        ");

            $stmt->execute([
                "puntaje" => $puntaje,
                "correctas" => $correctas,
                "id" => $resultId
            ]);

            // Actualizar puntos usuario
            $stmt = $db->prepare("
            UPDATE users
            SET puntos = puntos + :puntos
            WHERE id = :user_id
        ");

            $stmt->execute([
                "puntos" => $puntosGanados,
                "user_id" => $userId
            ]);

            // Registrar historial
            $stmt = $db->prepare("
            INSERT INTO points_history (user_id, puntos, motivo)
            VALUES (:user_id, :puntos, :motivo)
        ");

            $stmt->execute([
                "user_id" => $userId,
                "puntos" => $puntosGanados,
                "motivo" => "Examen ID $examId completado"
            ]);
            $db->commit();

            // finalmente retornamos el id del resultado, el puntaje(redondeamos), correctas, total de preguntas, y puntos ganados
            return [
                "result_id" => $resultId,
                "puntaje" => round($puntaje, 2),
                "correctas" => $correctas,
                "total" => $total,
                "puntos_ganados" => $puntosGanados
            ];
        } catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
    $db->rollBack();
    exit;
        }
    }
}

<?php
require_once __DIR__ . '/../core/Model.php';

class ExamAnswer extends Model
{
    protected static string $table = 'exam_answers';

public static function create(array $data, PDO $db)
{
    $stmt = $db->prepare("
        INSERT INTO exam_answers
        (exam_result_id, question_id, selected_option_id, es_correcta)
        VALUES (:exam_result_id, :question_id, :selected_option_id, :es_correcta)
    ");

    return $stmt->execute([
        'exam_result_id' => $data['exam_result_id'],
        'question_id' => $data['question_id'],
        'selected_option_id' => $data['selected_option_id'],
        'es_correcta' => $data['es_correcta']
    ]);
}

    public static function getByResult(int $examResultId)
    {
        $db = Database::connect();

        $stmt = $db->prepare("
            SELECT * FROM exam_answers WHERE exam_result_id = :exam_result_id
        ");
        $stmt->execute(['exam_result_id' => $examResultId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
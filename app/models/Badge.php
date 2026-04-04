<?php

require_once __DIR__ . '/../core/Model.php';

class Badge extends Model
{
    protected static string $table = 'badges';

    public static function getUserBadges(int $userId)
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT b.* FROM badges b
                JOIN user_badges ub ON b.id = ub.badge_id
                WHERE ub.user_id = :id"
        );
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function all(): array
{
    $db = Database::connect();
    $stmt = $db->query("SELECT * FROM badges");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public static function find(int $badgeId): ?array
{
    $db = Database::connect();
    $stmt = $db->prepare(
        "SELECT * FROM badges WHERE id = :id"
    );
    $stmt->execute(['id' => $badgeId]);

    $badge = $stmt->fetch(PDO::FETCH_ASSOC);
    return $badge ?: null;
}

public static function create(array $data): int
{
    $db = Database::connect();
    $stmt = $db->prepare(
        "INSERT INTO badges (nombre, descripcion, icono_url)
         VALUES (:nombre, :descripcion, :icono_url)"
    );
    $stmt->execute([
        'nombre' => $data['nombre'],
        'descripcion' => $data['descripcion'],
        'icono_url' => $data['icono_url']
    ]);

    return (int) $db->lastInsertId();
}

}

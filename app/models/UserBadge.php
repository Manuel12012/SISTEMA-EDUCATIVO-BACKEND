<?php

require_once __DIR__ . "/../core/Model.php";

class UserBadge extends Model
{
    protected static string $table = "user_badges";

    /**
     * Asignar un badge a un usuario
     */
    public static function assign(int $userId, int $badgeId): bool
    {
        if (self::exists($userId, $badgeId)) {
            return false; // ya lo tiene
        }

        $db = Database::connect();
        $stmt = $db->prepare(
            "INSERT INTO user_badges (user_id, badge_id)
             VALUES (:user_id, :badge_id)"
        );

        return $stmt->execute([
            "user_id" => $userId,
            "badge_id" => $badgeId
        ]);
    }

    /**
     * Verifica si el usuario ya tiene el badge
     */
    public static function exists(int $userId, int $badgeId): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT 1 FROM user_badges
             WHERE user_id = :user_id AND badge_id = :badge_id"
        );
        $stmt->execute([
            "user_id" => $userId,
            "badge_id" => $badgeId
        ]);

        return (bool) $stmt->fetch();
    }

    /**
     * Obtener todos los badges de un usuario
     */
    public static function getBadgesByUser(int $userId): array
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT b.*, ub.obtenido_en
                FROM badges b
                INNER JOIN user_badges ub
                ON b.id = ub.badge_id
                WHERE ub.user_id = :user_id"
        );
        $stmt->execute(["user_id" => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Quitar un badge a un usuario (opcional)
     */
    public static function removeBadge(int $userId, int $badgeId): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare(
            "DELETE FROM user_badges
             WHERE user_id = :user_id AND badge_id = :badge_id"
        );

        return $stmt->execute([
            "user_id" => $userId,
            "badge_id" => $badgeId
        ]);
    }
}

<?php

class UserModel extends Model
{
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email AND actif = 1'
        );
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE id = :id AND actif = 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function countUnreadNotifications(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = :id AND lu = 0'
        );
        $stmt->execute([':id' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}

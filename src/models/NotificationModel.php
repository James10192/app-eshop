<?php

class NotificationModel extends Model
{
    public function findByUser(int $user_id, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim'
        );
        $stmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit,   PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnread(int $user_id): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND lu = 0'
        );
        $stmt->execute([':uid' => $user_id]);
        return (int) $stmt->fetchColumn();
    }

    public function marquerLu(int $id, int $user_id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE notifications SET lu = 1, lu_at = NOW() WHERE id = :id AND user_id = :uid'
        );
        $stmt->execute([':id' => $id, ':uid' => $user_id]);
    }

    public function marquerTousLus(int $user_id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE notifications SET lu = 1, lu_at = NOW() WHERE user_id = :uid AND lu = 0'
        );
        $stmt->execute([':uid' => $user_id]);
    }

    public function create(int $user_id, string $type, string $titre, string $message, ?int $commande_id = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (user_id, type, titre, message, commande_id)
             VALUES (:uid, :type, :titre, :message, :commande_id)'
        );
        $stmt->execute([
            ':uid'         => $user_id,
            ':type'        => $type,
            ':titre'       => $titre,
            ':message'     => $message,
            ':commande_id' => $commande_id,
        ]);
    }
}

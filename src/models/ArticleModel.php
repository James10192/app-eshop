<?php

class ArticleModel extends Model
{
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, f.nom AS fournisseur_nom
             FROM articles a
             JOIN fournisseurs f ON f.id = a.fournisseur_id
             WHERE a.id = :id AND a.actif = 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByFournisseur(int $fournisseur_id): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM articles WHERE fournisseur_id = :fid AND actif = 1 ORDER BY nom'
        );
        $stmt->execute([':fid' => $fournisseur_id]);
        return $stmt->fetchAll();
    }

    /**
     * Retourne les articles des fournisseurs partenaires d'une entreprise
     */
    public function findByEntreprise(int $entreprise_id): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, f.nom AS fournisseur_nom
             FROM articles a
             JOIN fournisseurs f ON f.id = a.fournisseur_id
             JOIN entreprise_fournisseur ef ON ef.fournisseur_id = f.id
             WHERE ef.entreprise_id = :eid AND a.actif = 1 AND f.actif = 1
             ORDER BY f.nom, a.nom'
        );
        $stmt->execute([':eid' => $entreprise_id]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO articles (fournisseur_id, nom, description, prix, stock)
             VALUES (:fid, :nom, :description, :prix, :stock)'
        );
        $stmt->execute([
            ':fid'         => $data['fournisseur_id'],
            ':nom'         => $data['nom'],
            ':description' => $data['description'] ?? null,
            ':prix'        => $data['prix'],
            ':stock'       => $data['stock'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE articles SET nom = :nom, description = :description, prix = :prix, stock = :stock
             WHERE id = :id'
        );
        $stmt->execute([
            ':nom'         => $data['nom'],
            ':description' => $data['description'] ?? null,
            ':prix'        => $data['prix'],
            ':stock'       => $data['stock'] ?? 0,
            ':id'          => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE articles SET actif = 0 WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}

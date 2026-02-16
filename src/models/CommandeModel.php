<?php

class CommandeModel extends Model
{
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, u.nom AS employe_nom, u.prenom AS employe_prenom, e.nom AS entreprise_nom
             FROM commandes c
             JOIN employes emp ON emp.id = c.employe_id
             JOIN users u ON u.id = emp.user_id
             JOIN entreprises e ON e.id = c.entreprise_id
             WHERE c.id = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByEmploye(int $employe_id): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM commandes WHERE employe_id = :id ORDER BY created_at DESC'
        );
        $stmt->execute([':id' => $employe_id]);
        return $stmt->fetchAll();
    }

    public function findByEntreprise(int $entreprise_id, string $statut = ''): array
    {
        $sql = 'SELECT c.*, u.nom AS employe_nom, u.prenom AS employe_prenom
                FROM commandes c
                JOIN employes emp ON emp.id = c.employe_id
                JOIN users u ON u.id = emp.user_id
                WHERE c.entreprise_id = :eid';
        $params = [':eid' => $entreprise_id];

        if ($statut !== '') {
            $sql .= ' AND c.statut = :statut';
            $params[':statut'] = $statut;
        }

        $sql .= ' ORDER BY c.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO commandes (employe_id, entreprise_id, beneficiaire, beneficiaire_nom, montant_prev, note_employe)
             VALUES (:employe_id, :entreprise_id, :beneficiaire, :beneficiaire_nom, :montant_prev, :note_employe)'
        );
        $stmt->execute([
            ':employe_id'      => $data['employe_id'],
            ':entreprise_id'   => $data['entreprise_id'],
            ':beneficiaire'    => $data['beneficiaire'],
            ':beneficiaire_nom'=> $data['beneficiaire_nom'] ?? null,
            ':montant_prev'    => $data['montant_prev'],
            ':note_employe'    => $data['note_employe'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatut(int $id, string $statut, string $note = ''): void
    {
        $stmt = $this->db->prepare(
            'UPDATE commandes SET statut = :statut, note_entreprise = :note WHERE id = :id'
        );
        $stmt->execute([':statut' => $statut, ':note' => $note ?: null, ':id' => $id]);
    }
}

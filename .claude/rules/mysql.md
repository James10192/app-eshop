# Règles MySQL — App Eshop

## Connexion — Singleton PDO

```php
// config/database.php
class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                $_ENV['DB_HOST'],
                $_ENV['DB_NAME']
            );
            self::$instance = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
}
```

## Conventions de Schéma

```sql
-- Toujours InnoDB pour les FK
-- Toujours utf8mb4 + collation unicode_ci
-- Toujours BIGINT UNSIGNED AUTO_INCREMENT pour les PK
-- Toujours created_at + updated_at sur chaque table

CREATE TABLE commandes (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employe_id      BIGINT UNSIGNED NOT NULL,
    entreprise_id   BIGINT UNSIGNED NOT NULL,
    statut          ENUM('en_attente','valide','refuse','en_cours','termine') NOT NULL DEFAULT 'en_attente',
    montant_prev    DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    montant_reel    DECIMAL(15,2)   NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_commandes_employe    FOREIGN KEY (employe_id)    REFERENCES employes(id)    ON DELETE RESTRICT,
    CONSTRAINT fk_commandes_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Requêtes Préparées — Obligatoire

```php
// INTERDIT
$db->query("SELECT * FROM users WHERE email = '$email'");

// CORRECT — named placeholders
$stmt = $db->prepare('SELECT * FROM users WHERE email = :email AND role = :role');
$stmt->execute([':email' => $email, ':role' => $role]);
$user = $stmt->fetch();

// CORRECT — liste d'IDs avec IN (construire dynamiquement)
$ids = [1, 2, 3];
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $db->prepare("SELECT * FROM articles WHERE id IN ($placeholders)");
$stmt->execute($ids);
```

## Transactions — Opérations Multi-Write

```php
// Toujours une transaction pour plusieurs écritures liées
try {
    $db->beginTransaction();

    // 1. Créer la commande
    $stmt = $db->prepare('INSERT INTO commandes (employe_id, entreprise_id, montant_prev) VALUES (:emp, :ent, :montant)');
    $stmt->execute([':emp' => $employeId, ':ent' => $entrepriseId, ':montant' => $montant]);
    $commandeId = (int) $db->lastInsertId();

    // 2. Insérer les articles
    foreach ($articles as $article) {
        $stmt = $db->prepare('INSERT INTO commande_articles (commande_id, article_id, quantite, prix_unitaire) VALUES (:cid, :aid, :qty, :prix)');
        $stmt->execute([':cid' => $commandeId, ':aid' => $article['id'], ':qty' => $article['quantite'], ':prix' => $article['prix']]);
    }

    $db->commit();
    return $commandeId;

} catch (PDOException $e) {
    $db->rollBack();
    error_log('Transaction failed: ' . $e->getMessage());
    throw $e;
}
```

## Génération du Code Retrait (10 chiffres)

```php
// Générer un code unique 10 chiffres sans collision
function generateCodeRetrait(PDO $db): string {
    do {
        $code = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        $stmt = $db->prepare('SELECT COUNT(*) FROM codes_validation WHERE code = :code');
        $stmt->execute([':code' => $code]);
        $exists = (bool) $stmt->fetchColumn();
    } while ($exists);

    return $code;
}
```

## Calcul Date J+3 Jours Ouvrés

```php
function addBusinessDays(DateTime $date, int $days): DateTime {
    $result = clone $date;
    $added  = 0;
    while ($added < $days) {
        $result->modify('+1 day');
        $dow = (int) $result->format('N'); // 1=Lun, 7=Dim
        if ($dow < 6) { // exclure samedi (6) et dimanche (7)
            $added++;
        }
    }
    return $result;
}

// Usage après validation fournisseur
$dateRecuperation = addBusinessDays(new DateTime(), 3);
```

## Types de Colonnes — Règles

| Données | Type MySQL |
|---------|-----------|
| Identifiants | `BIGINT UNSIGNED` |
| Montants/Prix | `DECIMAL(15,2)` — jamais FLOAT |
| Statuts/Rôles | `ENUM(...)` |
| Codes retrait | `CHAR(10)` |
| Textes courts | `VARCHAR(255)` |
| Textes longs | `TEXT` |
| Dates seules | `DATE` |
| Dates + heures | `DATETIME` |
| Booléens | `TINYINT(1)` |
| Fichiers (chemin) | `VARCHAR(500)` |

## Migrations — Fichiers SQL Versionnés

```
database/
├── schema.sql           ← schéma complet (référence)
└── migrations/
    ├── 001_initial.sql
    ├── 002_add_retraits.sql
    └── ...
```

Jamais modifier une migration déjà exécutée — créer une nouvelle.

## Index — Performance

```sql
-- Index sur les colonnes fréquemment filtrées
CREATE INDEX idx_commandes_statut      ON commandes(statut);
CREATE INDEX idx_commandes_entreprise  ON commandes(entreprise_id);
CREATE INDEX idx_notifications_user    ON notifications(user_id, lu);
CREATE INDEX idx_codes_validation_code ON codes_validation(code);
```

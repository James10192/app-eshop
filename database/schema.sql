-- ============================================================
-- App Eshop — Schéma MySQL complet
-- Charset : utf8mb4 | Engine : InnoDB | Collation : unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Table : entreprises
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS entreprises (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(255)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    telephone       VARCHAR(20)     NULL,
    adresse         TEXT            NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : fournisseurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS fournisseurs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(255)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    telephone       VARCHAR(20)     NULL,
    adresse         TEXT            NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : entreprise_fournisseur (association)
-- Une entreprise peut avoir plusieurs fournisseurs partenaires
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS entreprise_fournisseur (
    entreprise_id   BIGINT UNSIGNED NOT NULL,
    fournisseur_id  BIGINT UNSIGNED NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (entreprise_id, fournisseur_id),
    CONSTRAINT fk_ef_entreprise  FOREIGN KEY (entreprise_id)  REFERENCES entreprises(id)  ON DELETE CASCADE,
    CONSTRAINT fk_ef_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : users
-- Tous les acteurs partagent cette table (rôle = discriminant)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(255)    NOT NULL,
    prenom          VARCHAR(255)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    password_hash   VARCHAR(255)    NOT NULL,
    role            ENUM('employe','entreprise','fournisseur','admin','super_admin') NOT NULL,
    entreprise_id   BIGINT UNSIGNED NULL,
    fournisseur_id  BIGINT UNSIGNED NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_entreprise  FOREIGN KEY (entreprise_id)  REFERENCES entreprises(id)  ON DELETE SET NULL,
    CONSTRAINT fk_users_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : employes
-- Informations complémentaires des employés
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS employes (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL UNIQUE,
    entreprise_id   BIGINT UNSIGNED NOT NULL,
    matricule       VARCHAR(50)     NULL UNIQUE,
    poste           VARCHAR(255)    NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_employes_user       FOREIGN KEY (user_id)       REFERENCES users(id)        ON DELETE CASCADE,
    CONSTRAINT fk_employes_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprises(id)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : articles
-- Catalogue produits d'un fournisseur
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS articles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fournisseur_id  BIGINT UNSIGNED NOT NULL,
    nom             VARCHAR(255)    NOT NULL,
    description     TEXT            NULL,
    prix            DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    stock           INT             NOT NULL DEFAULT 0,
    image           VARCHAR(500)    NULL,
    actif           TINYINT(1)      NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_articles_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : commandes
-- Demande de prêt principale soumise par un employé
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS commandes (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    employe_id      BIGINT UNSIGNED NOT NULL,
    entreprise_id   BIGINT UNSIGNED NOT NULL,
    beneficiaire    ENUM('self','proche')   NOT NULL DEFAULT 'self',
    beneficiaire_nom    VARCHAR(255)    NULL,
    statut          ENUM('en_attente','valide','refuse','en_cours','termine') NOT NULL DEFAULT 'en_attente',
    montant_prev    DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    montant_reel    DECIMAL(15,2)   NULL,
    note_employe    TEXT            NULL,
    note_entreprise TEXT            NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_commandes_employe    FOREIGN KEY (employe_id)    REFERENCES employes(id)    ON DELETE RESTRICT,
    CONSTRAINT fk_commandes_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : commande_articles
-- Détail des articles dans une commande
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS commande_articles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commande_id     BIGINT UNSIGNED NOT NULL,
    article_id      BIGINT UNSIGNED NOT NULL,
    quantite        INT UNSIGNED    NOT NULL DEFAULT 1,
    prix_unitaire   DECIMAL(15,2)   NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ca_commande FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    CONSTRAINT fk_ca_article  FOREIGN KEY (article_id)  REFERENCES articles(id)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : sous_commandes
-- Sous-commande générée par fournisseur après validation entreprise
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sous_commandes (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commande_id     BIGINT UNSIGNED NOT NULL,
    fournisseur_id  BIGINT UNSIGNED NOT NULL,
    statut          ENUM('en_attente','valide','valide_partiel','refuse') NOT NULL DEFAULT 'en_attente',
    montant_prev    DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    montant_reel    DECIMAL(15,2)   NULL,
    note_fournisseur TEXT           NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_sc_commande    FOREIGN KEY (commande_id)    REFERENCES commandes(id)    ON DELETE CASCADE,
    CONSTRAINT fk_sc_fournisseur FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : sous_commande_articles
-- Détail des articles dans une sous-commande (avec disponibilité)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sous_commande_articles (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sous_commande_id    BIGINT UNSIGNED NOT NULL,
    commande_article_id BIGINT UNSIGNED NOT NULL,
    disponible          TINYINT(1)      NOT NULL DEFAULT 1,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sca_sous_commande     FOREIGN KEY (sous_commande_id)    REFERENCES sous_commandes(id)      ON DELETE CASCADE,
    CONSTRAINT fk_sca_commande_article  FOREIGN KEY (commande_article_id) REFERENCES commande_articles(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : codes_validation
-- Code retrait 10 chiffres généré après validation fournisseur
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS codes_validation (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sous_commande_id    BIGINT UNSIGNED NOT NULL UNIQUE,
    code                CHAR(10)        NOT NULL UNIQUE,
    date_recuperation   DATE            NOT NULL,
    utilise             TINYINT(1)      NOT NULL DEFAULT 0,
    utilise_at          DATETIME        NULL,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cv_sous_commande FOREIGN KEY (sous_commande_id) REFERENCES sous_commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : documents
-- Documents joints à une commande (carte employé, bon de règlement)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS documents (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    commande_id     BIGINT UNSIGNED NOT NULL,
    type            ENUM('carte_employe','bon_reglement','autre') NOT NULL,
    nom_fichier     VARCHAR(500)    NOT NULL,
    mime_type       VARCHAR(100)    NOT NULL,
    taille          INT UNSIGNED    NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_commande FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : notifications
-- Notifications in-app (badge sur icône)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    type            VARCHAR(50)     NOT NULL,
    titre           VARCHAR(255)    NOT NULL,
    message         TEXT            NOT NULL,
    lu              TINYINT(1)      NOT NULL DEFAULT 0,
    lu_at           DATETIME        NULL,
    commande_id     BIGINT UNSIGNED NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user     FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
    CONSTRAINT fk_notif_commande FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Index de performance
-- ------------------------------------------------------------
CREATE INDEX idx_commandes_statut        ON commandes(statut);
CREATE INDEX idx_commandes_employe       ON commandes(employe_id);
CREATE INDEX idx_commandes_entreprise    ON commandes(entreprise_id);
CREATE INDEX idx_sous_commandes_statut   ON sous_commandes(statut);
CREATE INDEX idx_sous_commandes_fourn    ON sous_commandes(fournisseur_id);
CREATE INDEX idx_articles_fournisseur    ON articles(fournisseur_id);
CREATE INDEX idx_articles_actif          ON articles(actif);
CREATE INDEX idx_notifications_user      ON notifications(user_id, lu);
CREATE INDEX idx_codes_validation_code   ON codes_validation(code);
CREATE INDEX idx_users_role              ON users(role);
CREATE INDEX idx_users_entreprise        ON users(entreprise_id);

SET FOREIGN_KEY_CHECKS = 1;

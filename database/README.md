# database/

Schéma MySQL et migrations versionnées.

## Fichiers

| Fichier | Rôle |
|---------|------|
| `schema.sql` | Schéma complet de référence — toutes les tables dans leur état final |

## Migrations (`migrations/`)

Les migrations sont des fichiers SQL **incrémentiels** numérotés.

**Règle absolue** : ne jamais modifier une migration déjà exécutée. Créer une nouvelle.

```
migrations/
├── 001_initial.sql              ← Créé automatiquement depuis schema.sql
├── 002_add_xxxxx.sql
└── ...
```

## Tables du schéma

| Table | Description |
|-------|-------------|
| `entreprises` | Entreprises clientes |
| `fournisseurs` | Fournisseurs partenaires |
| `entreprise_fournisseur` | Association N:N entreprises ↔ fournisseurs |
| `users` | Tous les acteurs (rôle = discriminant) |
| `employes` | Données complémentaires employés |
| `articles` | Catalogue produits par fournisseur |
| `commandes` | Demandes de prêt principales |
| `commande_articles` | Lignes d'articles d'une commande |
| `sous_commandes` | Sous-commandes générées par fournisseur |
| `sous_commande_articles` | Lignes avec flag disponibilité |
| `codes_validation` | Codes retrait 10 chiffres |
| `documents` | Fichiers joints aux commandes |
| `notifications` | Notifications in-app |

## Commandes utiles

```bash
# Créer la base
mysql -u root -p -e "CREATE DATABASE app_eshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importer le schéma complet
mysql -u root -p app_eshop < database/schema.sql

# Appliquer une migration
mysql -u root -p app_eshop < database/migrations/002_add_xxxxx.sql
```

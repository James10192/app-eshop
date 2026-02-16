# src/models/

Couche d'accès aux données — une classe par entité principale.

## Règles

- Toutes les classes héritent de `Model` (`Model.php`)
- Uniquement PDO avec requêtes préparées — **jamais d'interpolation SQL**
- Pas de logique métier complexe ici (appartient aux controllers)
- Nommage : `NomEntiteModel.php` (ex : `CommandeModel.php`)

## Fichiers

| Fichier | Entité | Rôle |
|---------|--------|------|
| `Model.php` | Base | Classe abstraite, injecte PDO |
| `UserModel.php` | `users` | Auth, recherche par email/id, comptage notifications |
| `CommandeModel.php` | `commandes` | CRUD commandes, recherche par employé/entreprise |
| `ArticleModel.php` | `articles` | Catalogue fournisseur, articles par entreprise |
| `NotificationModel.php` | `notifications` | Listing, comptage non-lus, marquer comme lus |

## Modèles à créer (phases suivantes)

| Fichier | Phase |
|---------|-------|
| `SousCommandeModel.php` | Phase 2 |
| `DocumentModel.php` | Phase 2 |
| `CodeValidationModel.php` | Phase 3 |
| `EntrepriseModel.php` | Phase 4 |
| `FournisseurModel.php` | Phase 4 |

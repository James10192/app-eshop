# src/controllers/

Logique métier — orchestration entre modèles et vues.

## Règles

- Chaque méthode publique = une action HTTP (index, show, create, store, edit, update, delete)
- Toujours appeler `requireAuth()` ou `requireRole()` en début de méthode protégée
- Toujours appeler `verifyCsrf()` au début de chaque action POST
- Redirection via `header('Location: ...')` + `exit` après toute écriture
- Inclure la vue avec `require_once VIEW_PATH . '/...'`

## Fichiers

| Fichier | Rôle |
|---------|------|
| `AuthController.php` | Login, logout |
| `DashboardController.php` | Redirection selon le rôle |

## Contrôleurs à créer (phases suivantes)

| Fichier | Phase | Rôle |
|---------|-------|------|
| `CommandeController.php` | Phase 2 | Création et suivi commandes employé |
| `CatalogueController.php` | Phase 2 | Catalogue articles par fournisseur |
| `EntrepriseController.php` | Phase 2 | Validation/refus commandes |
| `FournisseurController.php` | Phase 3 | Validation sous-commandes, retraits |
| `NotificationController.php` | Phase 3 | API AJAX notifications |
| `AdminController.php` | Phase 4 | Dashboard super admin |

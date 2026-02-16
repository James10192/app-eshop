# src/views/commandes/

Vues liées aux commandes (côté employé).

## Fichiers à créer (Phase 2)

| Fichier | Type | URL | Description |
|---------|------|-----|-------------|
| `index.php` | Page complète | `GET /commandes` | Liste des commandes de l'employé connecté |
| `show.php` | Page complète | `GET /commandes/{id}` | Détail complet d'une commande |
| `modal-create.php` | Modal AJAX | `GET /commandes/create` | Formulaire de création (sélection articles + bénéficiaire + upload docs) |
| `modal-show.php` | Modal AJAX | `GET /commandes/{id}/modal` | Résumé rapide d'une commande (statut, articles, code retrait) |

## Convention

- `modal-create.php` et `modal-show.php` ne contiennent **que** la `<div class="modal ...">`, sans `<html>`
- `index.php` affiche un tableau Bootstrap responsive avec boutons "Voir" (`data-modal-url`) et "Détail" (lien direct)
- `show.php` affiche toutes les informations : articles, documents, sous-commandes, codes retrait, historique

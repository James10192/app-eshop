# src/views/fournisseur/

Vues de l'interface fournisseur.

## Fichiers à créer (Phase 3)

| Fichier | Type | URL | Description |
|---------|------|-----|-------------|
| `index.php` | Page complète | `GET /fournisseur/sous-commandes` | Liste des sous-commandes à traiter |
| `show.php` | Page complète | `GET /fournisseur/sous-commandes/{id}` | Détail complet avec liste articles et disponibilité |
| `modal-valider.php` | Modal AJAX | `GET /fournisseur/sous-commandes/{id}/modal-valider` | Validation totale ou partielle (cocher articles indisponibles) |
| `modal-retrait.php` | Modal AJAX | `GET /fournisseur/sous-commandes/{id}/modal-retrait` | Saisie du code retrait pour valider le retrait |
| `modal-article-create.php` | Modal AJAX | `GET /fournisseur/articles/create` | Formulaire d'ajout d'un article au catalogue |
| `modal-article-edit.php` | Modal AJAX | `GET /fournisseur/articles/{id}/edit` | Formulaire de modification d'un article |
| `catalogue.php` | Page complète | `GET /fournisseur/catalogue` | Gestion du catalogue articles |

## Notes

- Après validation fournisseur : code retrait 10 chiffres généré + date J+3 jours ouvrés calculée automatiquement
- La validation partielle coche les articles indisponibles → montant réel recalculé

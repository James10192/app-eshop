# src/views/entreprise/

Vues de l'interface gestionnaire entreprise.

## Fichiers à créer (Phase 2)

| Fichier | Type | URL | Description |
|---------|------|-----|-------------|
| `index.php` | Page complète | `GET /entreprise/commandes` | Liste des commandes à valider + tableau de bord |
| `show.php` | Page complète | `GET /entreprise/commandes/{id}` | Détail complet d'une commande avec documents joints |
| `modal-valider.php` | Modal AJAX | `GET /entreprise/commandes/{id}/modal-valider` | Formulaire de validation (note optionnelle) |
| `modal-refuser.php` | Modal AJAX | `GET /entreprise/commandes/{id}/modal-refuser` | Formulaire de refus avec motif obligatoire |
| `modal-show.php` | Modal AJAX | `GET /entreprise/commandes/{id}/modal` | Résumé rapide statut + montant |

## Notes

- La validation génère automatiquement les sous-commandes par fournisseur
- Les documents (carte employé, bon de règlement) sont consultables depuis `show.php`

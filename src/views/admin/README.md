# src/views/admin/

Vues de l'interface administrateur.

## Fichiers à créer (Phase 4)

| Fichier | Type | URL | Description |
|---------|------|-----|-------------|
| `index.php` | Page complète | `GET /admin` | Dashboard : KPIs, transactions récentes, santé plateforme |
| `entreprises.php` | Page complète | `GET /admin/entreprises` | Liste et gestion des entreprises |
| `fournisseurs.php` | Page complète | `GET /admin/fournisseurs` | Liste et gestion des fournisseurs |
| `tresorerie.php` | Page complète | `GET /admin/tresorerie` | Suivi montants prévisionnels vs réels |
| `modal-entreprise-create.php` | Modal AJAX | `GET /admin/entreprises/create` | Création entreprise (Super Admin uniquement) |
| `modal-entreprise-edit.php` | Modal AJAX | `GET /admin/entreprises/{id}/edit` | Modification entreprise |
| `modal-fournisseur-create.php` | Modal AJAX | `GET /admin/fournisseurs/create` | Création fournisseur (Super Admin uniquement) |
| `modal-fournisseur-edit.php` | Modal AJAX | `GET /admin/fournisseurs/{id}/edit` | Modification fournisseur |

## Niveaux d'accès

- `admin` : lecture supervision + trésorerie
- `super_admin` : tout + création/modification entreprises et fournisseurs

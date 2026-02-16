# src/views/catalogue/

Vues du catalogue produits (côté employé).

## Fichiers à créer (Phase 2)

| Fichier | Type | URL | Description |
|---------|------|-----|-------------|
| `index.php` | Page complète | `GET /catalogue` | Grille d'articles groupés par fournisseur, avec sélection pour commande |
| `modal-article.php` | Modal AJAX | `GET /catalogue/articles/{id}/modal` | Détail d'un article (description, prix, stock, fournisseur) |

## Notes

- Les articles sont filtrés selon les fournisseurs partenaires de l'entreprise de l'employé
- La sélection d'articles alimente le panier (stocké en session ou JS local) avant redirection vers la création de commande

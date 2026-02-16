# src/

Code source de l'application — architecture MVC.

## Structure

```
src/
├── models/       ← Accès BDD et logique domaine
├── controllers/  ← Logique métier et orchestration
└── views/        ← Templates HTML (PHP natif)
```

## Modèles (`models/`)

Un modèle par entité. Héritent tous de `Model` (qui injecte le PDO).
Contiennent uniquement des méthodes de lecture/écriture BDD — pas de logique métier complexe.

## Contrôleurs (`controllers/`)

Reçoivent la requête depuis `public/index.php`, appellent les modèles, et incluent la vue appropriée.
Chaque méthode publique correspond à une action HTTP.

## Vues (`views/`)

Templates PHP natifs. Jamais de logique métier dans les vues — uniquement affichage.
Les données sont passées via des variables PHP simples.

### Convention modals

Les formulaires **create** et **edit** sont des modals Bootstrap, chargés dynamiquement via `data-modal-url`.
Les pages **show** proposent :
- Un **modal résumé** (chargé dynamiquement via `data-modal-url`)
- Une **page détail complète** accessible en URL directe

Voir [`views/README.md`](views/README.md) pour le détail.

# src/views/

Templates HTML (PHP natif) — uniquement l'affichage, pas de logique métier.

## Convention des vues — 3 types

### 1. Page complète (liste / détail)
Fichier inclus via le layout `base.php`. Définit `$content` avant d'appeler le layout.

```php
// Dans le controller
$pageTitle = 'Mes commandes';
$content   = VIEW_PATH . '/commandes/index.php';
require_once VIEW_PATH . '/layouts/base.php';
```

### 2. Modal Bootstrap (create / edit / show résumé)
Retourné via AJAX (`X-Requested-With: XMLHttpRequest`).
Le fichier de la modal ne contient que le `<div class="modal ...">`, sans `<html>` ni `<body>`.
Chargé dynamiquement côté JS via `data-modal-url`.

```html
<!-- Bouton qui ouvre la modal de création -->
<button class="btn btn-primary" data-modal-url="/commandes/create">
    Nouvelle commande
</button>

<!-- Bouton qui ouvre le résumé d'une commande -->
<button class="btn btn-link" data-modal-url="/commandes/42/modal">
    Voir
</button>
```

### 3. Page détail complète (show)
URL directe `/commandes/42`. Affiche toutes les informations de l'entité.

## Structure

```
views/
├── layouts/       ← base.php, navbar.php, flash.php
├── auth/          ← login.php (page autonome sans layout)
├── commandes/     ← index.php, show.php, modal-create.php, modal-show.php
├── catalogue/     ← index.php
├── fournisseur/   ← index.php, modal-valider.php
├── entreprise/    ← index.php, modal-valider.php, modal-refuser.php
├── admin/         ← index.php
└── errors/        ← 403.php, 404.php, 500.php
```

## Règles de sécurité

- **Toujours** `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` pour les données utilisateur
- **Jamais** de `echo $_POST[...]` ou `echo $_GET[...]` directement
- Les modals retournées en AJAX doivent inclure le token CSRF si elles contiennent un formulaire POST

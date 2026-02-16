# Règles Frontend — Bootstrap 5 + JS Vanilla

## Bootstrap 5 — Usage

```html
<!-- Vendor local (pas de CDN en production) -->
<link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Icônes Bootstrap Icons -->
<link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
```

## Composants Bootstrap Imposés par le CDC

### Badge de Notification sur Icône Menu
```html
<!-- Navbar — badge notification -->
<li class="nav-item">
    <a class="nav-link position-relative" href="/notifications">
        <i class="bi bi-bell-fill fs-5"></i>
        <?php if ($notifCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $notifCount ?>
                <span class="visually-hidden">notifications non lues</span>
            </span>
        <?php endif; ?>
    </a>
</li>
```

### Toast Flash Messages
```html
<!-- Afficher les messages flash (succès/erreur) -->
<?php if (!empty($_SESSION['flash_success'])): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body"><?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>
```

## JavaScript Vanilla — Règles

### Structure globale
```javascript
// public/assets/js/app.js
// Pas de framework, pas de jQuery
// Vanilla JS ES6+ uniquement

'use strict';

// Initialisation au chargement DOM
document.addEventListener('DOMContentLoaded', () => {
    initToasts();
    initNotifications();
    initFormValidation();
});
```

### Interdictions JS

```javascript
// INTERDIT — jQuery
$('#myModal').modal('show');

// INTERDIT — eval
eval(userInput);

// INTERDIT — innerHTML avec données utilisateur (XSS)
element.innerHTML = userData;

// CORRECT — Bootstrap JS vanilla
const modal = new bootstrap.Modal(document.getElementById('myModal'));
modal.show();

// CORRECT — textContent pour données utilisateur
element.textContent = userData;

// CORRECT — template pour HTML structuré
function createNotifItem(notif) {
    const li = document.createElement('li');
    li.className = 'list-group-item';
    const span = document.createElement('span');
    span.textContent = notif.message;
    li.appendChild(span);
    return li;
}
```

### Fetch API — Requêtes AJAX

```javascript
// Toujours async/await + gestion d'erreur
async function fetchNotifications() {
    try {
        const response = await fetch('/api/notifications', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();
        return data;
    } catch (err) {
        console.error('Notifications fetch error:', err);
        return [];
    }
}

// Protection CSRF sur les POST
async function postForm(url, formData) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    return fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-Token': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    });
}
```

## Validation Formulaire — Double Couche

```javascript
// Validation HTML5 native + feedback Bootstrap
function initFormValidation() {
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}
```

```html
<!-- Formulaire avec validation Bootstrap -->
<form method="POST" action="/commandes" data-validate novalidate>
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    <div class="mb-3">
        <label for="beneficiaire" class="form-label">Bénéficiaire *</label>
        <select class="form-select" id="beneficiaire" name="beneficiaire_type" required>
            <option value="">Choisir...</option>
            <option value="self">Moi-même</option>
            <option value="proche">Un proche</option>
        </select>
        <div class="invalid-feedback">Veuillez sélectionner un bénéficiaire.</div>
    </div>
</form>
```

## CSS — Conventions

```css
/* public/assets/css/style.css */
/* Surcharges Bootstrap uniquement — ne pas recréer ce que Bootstrap fait déjà */

:root {
    --bs-primary: #0d6efd;  /* Réutiliser les variables Bootstrap */
    --app-sidebar-width: 260px;
}

/* Namespacing par composant */
.app-navbar { }
.app-sidebar { }
.app-commande-card { }
.app-badge-statut { }

/* Statuts — couleurs sémantiques */
.statut-en-attente { color: var(--bs-warning); }
.statut-valide     { color: var(--bs-success); }
.statut-refuse     { color: var(--bs-danger); }
.statut-en-cours   { color: var(--bs-info); }
.statut-termine    { color: var(--bs-secondary); }
```

## Modals Bootstrap — Convention Obligatoire

### Règle : Create, Edit, Show-résumé = Modals. Index, Show-complet = Pages.

Les formulaires de création (`create`) et modification (`edit`) sont **toujours des modals** chargées dynamiquement via AJAX. Les pages `show` existent en deux formes : modal résumé + page détail complète.

### Attribut déclencheur : `data-modal-url`

```html
<!-- Bouton qui charge et ouvre une modal -->
<button class="btn btn-primary" data-modal-url="/commandes/create">
    Nouvelle commande
</button>

<button class="btn btn-sm btn-outline-primary" data-modal-url="/commandes/42/modal">
    Voir résumé
</button>

<button class="btn btn-sm btn-outline-secondary" data-modal-url="/articles/7/edit">
    Modifier
</button>
```

### Structure d'un fichier modal PHP

Le fichier `modal-create.php` retourne **uniquement** la `<div class="modal">` — pas de `<html>` ni `<body>`.

```php
<!-- src/views/commandes/modal-create.php -->
<div class="modal fade" id="modal-commande-create" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/commandes" data-validate novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <!-- champs du formulaire -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="form-commande" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
```

### Tailles de modals selon le contenu

| Contenu | Taille | Classe |
|---------|--------|--------|
| Confirmation simple | Petite | `modal-dialog` |
| Formulaire standard | Moyenne | `modal-dialog modal-lg` |
| Formulaire complexe (multi-articles) | Grande | `modal-dialog modal-xl` |
| Détail avec beaucoup de données | Scrollable | `modal-dialog modal-lg modal-dialog-scrollable` |

### Le chargeur JS est dans `app.js` (`initModalLoader`)

```javascript
// Tout élément avec data-modal-url déclenche le chargement AJAX automatiquement
// Voir public/assets/js/app.js → initModalLoader()
```

## Responsive — Mobile First

Toutes les pages doivent être utilisables sur mobile (les employés peuvent passer des commandes depuis leur téléphone) :

```html
<!-- Grid Bootstrap adaptatif -->
<div class="row g-3">
    <div class="col-12 col-md-6 col-lg-4">
        <!-- Carte article -->
    </div>
</div>

<!-- Table responsive -->
<div class="table-responsive">
    <table class="table table-hover align-middle">...</table>
</div>
```

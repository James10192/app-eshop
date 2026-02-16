# public/assets/

Fichiers statiques servis directement par le navigateur.

## Structure

```
assets/
├── css/
│   └── style.css          ← Surcharges Bootstrap + classes utilitaires app
├── js/
│   └── app.js             ← JS Vanilla ES6+ (toasts, validation, modals AJAX, notifications)
└── vendor/
    ├── bootstrap/
    │   ├── css/bootstrap.min.css
    │   └── js/bootstrap.bundle.min.js
    └── bootstrap-icons/
        └── bootstrap-icons.css
```

## Installation des vendors

Bootstrap 5 et Bootstrap Icons doivent être téléchargés et placés dans `vendor/` :

```bash
# Bootstrap 5
# https://getbootstrap.com/docs/5.3/getting-started/download/

# Bootstrap Icons
# https://icons.getbootstrap.com/#install
```

> Pas de CDN en production — les fichiers doivent être locaux.

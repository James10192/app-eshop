# public/

Seul dossier exposé par le serveur web — document root Apache/Nginx.

## Fichiers

| Fichier | Rôle |
|---------|------|
| `index.php` | Front controller unique — toutes les requêtes HTTP arrivent ici via `.htaccess` |

## Sous-dossiers

| Dossier | Contenu |
|---------|---------|
| `assets/css/` | `style.css` — surcharges Bootstrap |
| `assets/js/` | `app.js` — JS Vanilla ES6+ (toasts, validation, notifications, loader modals AJAX) |
| `assets/vendor/` | Bootstrap 5 + Bootstrap Icons (fichiers locaux — pas de CDN) |

## Configuration serveur

**Apache** : le `.htaccess` à la racine du projet redirige tout vers `public/index.php`.

**PHP built-in server** :
```bash
php -S localhost:8000 -t public/
```

**Nginx** :
```nginx
root /path/to/app-eshop/public;
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Sécurité

- Les dossiers `src/`, `config/`, `database/`, `storage/` ne doivent **jamais** être accessibles directement
- Le `.htaccess` et la configuration Nginx s'en chargent via le document root sur `public/`

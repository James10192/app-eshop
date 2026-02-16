# src/views/layouts/

Templates partagés inclus dans toutes les pages protégées.

## Fichiers

| Fichier | Rôle |
|---------|------|
| `base.php` | Layout principal — `<html>`, `<head>`, inclusion navbar + flash + `$content`, scripts |
| `navbar.php` | Barre de navigation Bootstrap avec badge notifications et menu utilisateur |
| `flash.php` | Toasts Bootstrap pour les messages flash (`$_SESSION['flash_success']` / `flash_error`) |

## Utilisation dans un controller

```php
$pageTitle = 'Titre de la page';
$content   = VIEW_PATH . '/commandes/index.php';
require_once VIEW_PATH . '/layouts/base.php';
```

## Variables attendues par base.php

| Variable | Type | Obligatoire | Description |
|----------|------|-------------|-------------|
| `$pageTitle` | string | Non | Titre de l'onglet (défaut : APP_NAME) |
| `$content` | string | Oui | Chemin absolu vers le fichier de contenu |
| `$extraJs` | array | Non | Scripts JS supplémentaires à charger en fin de page |

## Injection de scripts JS spécifiques à une page

```php
$extraJs = ['/assets/js/commandes.js'];
```

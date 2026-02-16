# config/

Fichiers de configuration globaux de l'application.

## Fichiers

| Fichier | Rôle |
|---------|------|
| `bootstrap.php` | Point d'entrée obligatoire — charge `.env`, démarre la session, définit les constantes, enregistre l'autoload, expose les fonctions globales (`requireAuth`, `requireRole`, `csrfToken`, etc.) |
| `database.php` | Singleton PDO — une seule connexion MySQL partagée dans toute l'app via `Database::getInstance()` |

## Constantes définies dans bootstrap.php

| Constante | Valeur |
|-----------|--------|
| `APP_NAME` | Nom de l'application |
| `APP_URL` | URL de base |
| `VIEW_PATH` | Chemin absolu vers `src/views/` |
| `UPLOAD_PATH` | Chemin absolu vers `storage/documents/` |
| `MAX_UPLOAD_SIZE` | 5 MB |
| `ALLOWED_MIME_TYPES` | `pdf`, `jpeg`, `png` |

## Utilisation

```php
// Dans tout fichier PHP chargé après bootstrap.php
$db = Database::getInstance(); // connexion PDO

requireAuth();                 // redirige vers /login si non connecté
requireRole('employe');        // redirige 403 si rôle incorrect
$token = csrfToken();          // génère ou retourne le token CSRF de session
verifyCsrf();                  // vérifie le token POST, die si invalide
```

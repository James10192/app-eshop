# src/views/auth/

Vues d'authentification — pages autonomes sans layout `base.php`.

## Fichiers

| Fichier | URL | Description |
|---------|-----|-------------|
| `login.php` | `GET /login` | Page de connexion — inclut Bootstrap directement, sans navbar |

## Notes

- Les pages auth sont autonomes (pas d'include de `base.php`) car l'utilisateur n'est pas connecté
- Pas de modal dans ce dossier — toutes les actions auth sont des redirections de pages complètes

# src/views/errors/

Pages d'erreur HTTP — autonomes, sans layout.

## Fichiers

| Fichier | Code HTTP | Déclenchement |
|---------|-----------|---------------|
| `403.php` | 403 | Accès refusé — `requireRole()` échoue |
| `404.php` | 404 | Route non trouvée dans `public/index.php` |
| `500.php` | 500 | Erreur serveur non gérée (à créer si besoin) |

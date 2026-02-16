# storage/

Fichiers générés par l'application — **non versionné** (ajouté dans `.gitignore`).

## Structure

```
storage/
└── documents/     ← Uploads employés (carte d'employé, bon de règlement)
```

## Règles de sécurité

- Ce dossier ne doit **jamais** être accessible directement via le navigateur
- Les fichiers uploadés sont servis uniquement via un controller PHP qui vérifie les droits
- Les noms de fichiers sont générés aléatoirement (`{commande_id}_{random_hex}.{ext}`)
- Types acceptés : PDF, JPEG, PNG — max 5 MB

## Permissions serveur

```bash
# Donner les droits d'écriture au serveur web (Linux)
chown -R www-data:www-data storage/
chmod -R 755 storage/
```

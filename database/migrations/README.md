# database/migrations/

Fichiers SQL de migration versionnés.

## Convention de nommage

```
NNN_description_courte.sql
```

- `NNN` : numéro à 3 chiffres avec zéro padding (`001`, `002`, ...)
- `description_courte` : snake_case, décrit l'action (`add_codes_validation`, `add_index_notifications`)

## Règles

1. **Jamais modifier** un fichier de migration déjà exécuté
2. **Toujours créer** un nouveau fichier pour chaque changement
3. Chaque fichier doit être **idempotent** si possible (`IF NOT EXISTS`, `IF EXISTS`)
4. Toujours tester sur une copie de la BDD avant d'exécuter en production

## Migrations à créer

| Fichier | Contenu | Phase |
|---------|---------|-------|
| `001_initial.sql` | Extraction du schéma complet initial | Phase 1 |

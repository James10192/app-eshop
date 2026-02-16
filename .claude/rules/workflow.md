# Règles Workflow de Développement — App Eshop

## Règle Fondamentale : Plan Before Code

**Jamais de code sans que le plan ait été approuvé.** Utiliser `/workflow:plan-and-confirm` pour tout changement non trivial. Un changement trivial = modifier une chaîne de caractères ou corriger une typo.

## Anti-Over-Engineering

- **3 lignes similaires valent mieux qu'une abstraction prématurée**
- **Ne pas créer d'helpers/utils pour un usage unique**
- **Ne pas ajouter de gestion d'erreurs pour des scénarios impossibles**
- **Ne pas concevoir pour des besoins hypothétiques futurs**
- **Préférer modifier un fichier existant plutôt que créer un nouveau**
- **Pas de feature flags ou shims rétrocompatibles** sauf si demandé explicitement

```php
// INTERDIT — abstraction inutile pour 2 usages
function formatStatutLabel(string $statut, string $role): string {
    return ucfirst($statut) . ' (' . $role . ')';
}

// CORRECT — inline si utilisé 1-2 fois
$label = ucfirst($commande['statut']);
```

## Ce Qu'on Ne Touche PAS Sans Demande

- Ne pas ajouter de commentaires PHPDoc sur du code non modifié
- Ne pas ajouter de commentaires évidents
- Ne pas refactorer le code adjacent au bug fixé
- Ne pas renommer des variables "pour la clarté" sans demande
- Ne pas convertir des `include` en `require_once` qui n'ont pas de rapport avec la tâche

## Format des Commits Git

```
<type>(<scope>): <description courte en impératif présent>

[corps optionnel — explication du POURQUOI si nécessaire]
```

**Types :** `feat`, `fix`, `refactor`, `docs`, `test`, `chore`, `style`

**Règles :**
- Description ≤ 72 caractères
- Impératif présent : "add commande validation" pas "added commande validation"
- PAS de "Generated with Claude Code" ou "Co-Authored-By" dans les commits
- Auteur des commits : `James10192 <djedjelipatrick@gmail.com>`

**Exemples corrects :**
```
feat(commandes): add multi-fournisseur order creation flow
fix(auth): prevent session fixation on login
feat(fournisseur): generate 10-digit retrait code after validation
```

## Git — Opérations Sûres

```bash
# Toujours ajouter explicitement (jamais git add -A ou git add .)
git add src/controllers/CommandeController.php database/schema.sql

# Vérifier avant commit
git diff --staged

# Commit
git commit -m "feat(commandes): add order creation with document upload"

# Créer une issue GitHub
gh issue create --title "..." --body "..." --label bug

# Créer une PR après review du diff
gh pr create --title "..." --body "..."

# Merger une PR après approbation
gh pr merge <number> --squash --delete-branch
```

## Vérification PHP Après Modification

```bash
# Toujours vérifier la syntaxe après modification d'un fichier PHP
php -l src/controllers/CommandeController.php

# Vérifier tous les fichiers PHP d'un dossier
find src/ -name "*.php" -exec php -l {} \;
```

## Séquence Complète Feature → Production

```
1. /workflow:plan-and-confirm  ← OBLIGATOIRE
2. Coder (un fichier à la fois, commits atomiques)
3. php -l sur chaque fichier PHP modifié
4. /workflow:code-review       ← vérifier avant PR
5. /workflow:create-pr         ← créer la PR
6. Merge après approbation
7. /anthropic:update-memory-bank ← si décisions architecturales importantes
```

## Sécurité — Règles Obligatoires

- **Toujours des requêtes préparées PDO** — jamais d'interpolation de variables dans SQL
- **Toujours `htmlspecialchars()`** avant d'afficher une donnée utilisateur en HTML
- **CSRF token** sur tous les formulaires POST (générer via `bin2hex(random_bytes(32))`)
- **Validation serveur** avant toute opération BDD — jamais se fier uniquement au JS
- **Vérifier le rôle** dans chaque controller protégé via `requireRole()`
- **Jamais de secrets dans le code** — toujours `.env`
- **Uploads** : valider MIME type réel (finfo), pas l'extension, limiter la taille

## Phases de Développement

### Phase 1 — Fondations (en cours)
- Structure MVC + front controller
- Schéma MySQL complet
- Authentification (login/logout/sessions)
- Layout de base + navbar avec badge notifications
- README.md dans chaque dossier

### Phase 2 — Catalogue & Commandes
- Catalogue articles par fournisseur (vue employé)
- Création de commande multi-fournisseurs
- Upload de documents (carte employé + bon de règlement)
- Validation/refus par l'entreprise
- Génération automatique des sous-commandes par fournisseur

### Phase 3 — Validation & Retraits
- Interface fournisseur (validation totale/partielle)
- Génération code retrait 10 chiffres
- Calcul date récupération J+3 jours ouvrés
- Processus de retrait (bénéficiaire → fournisseur valide)
- Notifications email + badge

### Phase 4 — Trésorerie & Admin
- Calcul montant prévisionnel vs réel
- Dashboard trésorerie (entreprise + admins)
- Interface Super Admin (gestion entreprises/fournisseurs)
- Supervision complète de la plateforme

## Règle des Deux Corrections

Si Claude fait la même erreur **deux fois de suite** dans la même session :
1. Stopper immédiatement
2. Signaler : "J'ai fait cette erreur deux fois. Plutôt que de réessayer, reformulez le prompt en incluant la contrainte manquante."
3. Attendre la reformulation — ne pas essayer une troisième fois

# currentDate
Today's date is 2026-02-16.

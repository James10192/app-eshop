# CLAUDE.md — App Eshop

## Contexte du projet

Application e-commerce de gestion de prêt inter-entreprises.
Permet aux employés d'acheter des produits auprès de fournisseurs partenaires via un système de prêt validé par leur entreprise.

**Stack technique :**
- Backend : PHP (MVC sans framework)
- Base de données : MySQL (PDO)
- Frontend : JavaScript Vanilla ES6+
- UI : Bootstrap 5 + Bootstrap Icons
- Serveur : Apache/Nginx avec `.htaccess`

---

## Architecture du projet

```
app-eshop/
├── CLAUDE.md                    ← ce fichier
├── .env                         ← variables d'environnement (jamais commité)
├── .env.example                 ← template .env
├── .htaccess                    ← réécriture d'URL vers public/
├── config/
│   ├── bootstrap.php            ← session, constantes, autoload
│   └── database.php             ← singleton PDO
├── src/
│   ├── models/                  ← accès BDD, logique domaine
│   ├── controllers/             ← logique métier, orchestration
│   └── views/                   ← templates HTML (PHP natif)
│       ├── layouts/             ← base.php, navbar.php
│       ├── auth/                ← login.php
│       ├── commandes/           ← index, create, show
│       ├── catalogue/           ← index, show
│       ├── fournisseur/         ← dashboard, sous-commandes
│       ├── entreprise/          ← dashboard, validation
│       ├── admin/               ← dashboard super admin
│       └── errors/              ← 403.php, 404.php, 500.php
├── public/
│   ├── index.php                ← front controller unique
│   └── assets/
│       ├── css/style.css
│       ├── js/app.js
│       └── vendor/              ← bootstrap, bootstrap-icons
├── database/
│   ├── schema.sql               ← schéma complet de référence
│   └── migrations/              ← fichiers SQL versionnés
└── storage/
    └── documents/               ← uploads employés (gitignored)
```

---

## Acteurs et rôles

| Rôle | Valeur en BDD | Accès |
|------|--------------|-------|
| Employé | `employe` | Catalogue, créer commandes, voir ses commandes |
| Gestionnaire entreprise | `entreprise` | Valider/refuser commandes, dashboard trésorerie |
| Fournisseur | `fournisseur` | Gérer catalogue, valider sous-commandes, retraits |
| Administrateur | `admin` | Supervision plateforme |
| Super Administrateur | `super_admin` | Gestion entreprises/fournisseurs + admin |

---

## Flux métier principal

```
1. Employé crée une commande
   → Sélectionne articles (multi-fournisseurs)
   → Choisit bénéficiaire (lui-même ou un proche)
   → Joint carte employé + bon de règlement
   → Soumet la demande

2. Entreprise valide ou refuse
   → Refus → notification employé + admins → fin
   → Validation → sous-commandes générées par fournisseur

3. Chaque fournisseur valide sa sous-commande
   → Totalement ou partiellement (articles indisponibles cochés)
   → Génération code retrait 10 chiffres
   → Calcul date récupération J+3 jours ouvrés
   → Notifications : employé + entreprise + admins

4. Retrait
   → Bénéficiaire présente le code au fournisseur
   → Fournisseur valide le retrait
   → Notifications : entreprise + admins + employé

5. Trésorerie
   → Montant prévisionnel (à la commande)
   → Montant réel (après validation fournisseur)
   → Suivi des écarts (articles indisponibles)
```

---

## Commandes utiles

```bash
# Vérifier la syntaxe d'un fichier PHP
php -l src/controllers/CommandeController.php

# Vérifier tous les fichiers PHP
find src/ -name "*.php" -exec php -l {} \;

# Importer le schéma MySQL
mysql -u root -p app_eshop < database/schema.sql

# Lancer le serveur de développement PHP
php -S localhost:8000 -t public/

# Vérifier les logs d'erreur
tail -f /var/log/apache2/error.log
```

---

## Variables d'environnement requises (`.env`)

```ini
APP_ENV=development
APP_NAME="App Eshop"
APP_URL=http://localhost:8000

DB_HOST=localhost
DB_NAME=app_eshop
DB_USER=root
DB_PASS=

MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USER=
MAIL_PASS=
MAIL_FROM=noreply@app-eshop.com
```

---

## Règles de développement

Voir `.claude/rules/` pour les règles détaillées :
- [php.md](.claude/rules/php.md) — Architecture MVC, PDO, sessions, sécurité
- [mysql.md](.claude/rules/mysql.md) — Schéma, requêtes préparées, transactions
- [frontend.md](.claude/rules/frontend.md) — Bootstrap 5, JS Vanilla, validation formulaires
- [workflow.md](.claude/rules/workflow.md) — Git, commits, phases de développement

### Règles absolues

1. **Jamais de code sans plan approuvé** → utiliser `/workflow:plan-and-confirm`
2. **Toujours PDO avec requêtes préparées** → jamais `mysqli` ni interpolation SQL
3. **Toujours `htmlspecialchars()`** avant d'afficher une donnée utilisateur
4. **CSRF token** sur tous les formulaires POST
5. **`php -l`** après chaque modification d'un fichier PHP
6. **Commits atomiques** par fonctionnalité, pas de `git add -A`

---

## Phases de développement

### ✅ Phase 1 — Fondations
- [x] Structure MVC + front controller
- [x] Schéma MySQL complet
- [ ] Authentification (login/logout/sessions)
- [ ] Layout de base + navbar + badge notifications

### ⏳ Phase 2 — Catalogue & Commandes
- [ ] Catalogue articles par fournisseur
- [ ] Création de commande multi-fournisseurs
- [ ] Upload documents (carte employé + bon de règlement)
- [ ] Validation/refus par l'entreprise
- [ ] Génération des sous-commandes

### ⏳ Phase 3 — Validation & Retraits
- [ ] Interface fournisseur (validation totale/partielle)
- [ ] Génération code retrait 10 chiffres
- [ ] Calcul date J+3 jours ouvrés
- [ ] Processus de retrait

### ⏳ Phase 4 — Trésorerie & Admin
- [ ] Montant prévisionnel vs réel
- [ ] Dashboard trésorerie
- [ ] Interface Super Admin
- [ ] Notifications email

---

## Convention des vues — Modals vs Pages

### Règle générale

| Action | Type de vue | Chargement |
|--------|------------|------------|
| `create` | **Modal Bootstrap** | AJAX via `data-modal-url` |
| `edit` | **Modal Bootstrap** | AJAX via `data-modal-url` |
| `show` (résumé) | **Modal Bootstrap** | AJAX via `data-modal-url` |
| `show` (complet) | **Page complète** | URL directe |
| `index` (liste) | **Page complète** | URL directe |

### Fichiers de vues par action

- `modal-create.php` → `<div class="modal ...">` uniquement, sans `<html>`
- `modal-edit.php` → idem, pré-remplit le formulaire avec les données existantes
- `modal-show.php` → résumé rapide (statut, montant, code retrait si disponible)
- `show.php` → page complète, toutes les données, accessible en URL directe
- `index.php` → liste avec colonnes clés + boutons "Voir" (modal) et "Détail" (page)

### Exemple d'usage dans une vue index

```html
<!-- Bouton modal résumé -->
<button class="btn btn-sm btn-outline-primary" data-modal-url="/commandes/<?= $c['id'] ?>/modal">
    <i class="bi bi-eye"></i> Voir
</button>

<!-- Lien page complète -->
<a href="/commandes/<?= $c['id'] ?>" class="btn btn-sm btn-link">Détail</a>

<!-- Bouton modal création -->
<button class="btn btn-primary" data-modal-url="/commandes/create">
    <i class="bi bi-plus-lg"></i> Nouvelle commande
</button>
```

### Routes AJAX pour les modals

Les routes qui retournent une modal doivent détecter la requête AJAX :

```php
// Dans le controller
public function modalCreate(): void {
    // Retourne uniquement la <div class="modal">
    require_once VIEW_PATH . '/commandes/modal-create.php';
}

public function modalShow(int $id): void {
    $commande = (new CommandeModel())->findById($id);
    require_once VIEW_PATH . '/commandes/modal-show.php';
}
```

---

## Conventions de nommage rapides

| Élément | Convention | Exemple |
|---------|-----------|---------|
| Classes PHP | PascalCase | `CommandeController` |
| Méthodes | camelCase | `createCommande()` |
| Variables PHP | snake_case | `$commande_id` |
| Tables BDD | snake_case pluriel | `commandes`, `sous_commandes` |
| Colonnes BDD | snake_case | `created_at`, `entreprise_id` |
| Fichiers vues | kebab-case | `create-commande.php` |
| Routes URL | kebab-case | `/commandes/create` |

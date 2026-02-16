# App Eshop

Application e-commerce de gestion de prêt inter-entreprises.

## Contexte

Permet aux employés d'une entreprise d'acheter des produits auprès de fournisseurs partenaires via un système de prêt validé par leur entreprise.

## Stack technique

| Élément | Technologie |
|---------|------------|
| Backend | PHP (MVC sans framework) |
| Base de données | MySQL (PDO) |
| Frontend | JavaScript Vanilla ES6+ |
| UI | Bootstrap 5 + Bootstrap Icons |

## Démarrage rapide

```bash
# 1. Copier le fichier d'environnement
cp .env.example .env
# Remplir les variables dans .env

# 2. Créer la base de données MySQL
mysql -u root -p -e "CREATE DATABASE app_eshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. Importer le schéma
mysql -u root -p app_eshop < database/schema.sql

# 4. Lancer le serveur PHP de développement
php -S localhost:8000 -t public/

# 5. Ouvrir http://localhost:8000
```

## Structure du projet

```
app-eshop/
├── CLAUDE.md          ← Guide développement et règles IA
├── .env.example       ← Template variables d'environnement
├── .htaccess          ← Réécriture URL → public/
├── config/            ← Bootstrap PHP, connexion DB
├── src/               ← Code MVC (models, controllers, views)
├── public/            ← Point d'entrée + assets statiques
├── database/          ← Schéma SQL + migrations
└── storage/           ← Fichiers uploadés (gitignored)
```

## Acteurs

- **Employé** : Crée des commandes, consulte le catalogue
- **Entreprise** : Valide ou refuse les demandes de prêt
- **Fournisseur** : Gère le catalogue, valide les sous-commandes
- **Admin / Super Admin** : Supervision complète de la plateforme

## Phases de développement

| Phase | Contenu | Statut |
|-------|---------|--------|
| 1 | Fondations (auth, structure, BDD) | ✅ En cours |
| 2 | Catalogue & Commandes | ⏳ À venir |
| 3 | Validation & Retraits | ⏳ À venir |
| 4 | Trésorerie & Admin | ⏳ À venir |

Voir [CLAUDE.md](CLAUDE.md) pour le détail complet.

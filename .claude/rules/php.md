# Règles PHP — App Eshop

## Architecture MVC Simple (pas de framework)

```
public/index.php          ← front controller unique
src/
├── models/               ← accès BDD, logique domaine
├── controllers/          ← logique métier, orchestration
└── views/                ← templates HTML (PHP natif)
config/
└── database.php          ← singleton PDO
```

## Front Controller — Routing

Toutes les requêtes passent par `public/index.php` :

```php
// public/index.php
require_once __DIR__ . '/../config/bootstrap.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Supprimer le préfixe du sous-dossier si nécessaire
$uri = str_replace('/app-eshop/public', '', $uri) ?: '/';

$router->dispatch($method, $uri);
```

## Modèles — PDO Obligatoire (jamais mysqli)

```php
// src/models/Model.php — classe de base
abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}

// Toujours des requêtes préparées — jamais d'interpolation directe
$stmt = $this->db->prepare(
    'SELECT * FROM commandes WHERE entreprise_id = :id AND statut = :statut'
);
$stmt->execute([':id' => $id, ':statut' => $statut]);
```

## Interdictions Absolues

```php
// INTERDIT — injection SQL
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");

// INTERDIT — affichage brut sans échappement
echo $_POST['nom'];

// INTERDIT — include avec chemin utilisateur
include $_GET['page'] . '.php';

// CORRECT — toujours htmlspecialchars pour l'affichage
echo htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8');

// CORRECT — chemins hardcodés uniquement
require_once __DIR__ . '/../views/auth/login.php';
```

## Sessions — Gestion Sécurisée

```php
// config/bootstrap.php — toujours en premier
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

// Vérification d'auth dans chaque controller protégé
function requireAuth(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

// Vérification de rôle
function requireRole(string ...$roles): void {
    requireAuth();
    if (!in_array($_SESSION['user_role'], $roles, true)) {
        http_response_code(403);
        require_once VIEW_PATH . '/errors/403.php';
        exit;
    }
}
```

## Validation des Inputs — Toujours Côté Serveur

```php
// Valider AVANT toute opération BDD
function validateCommande(array $data): array {
    $errors = [];

    if (empty($data['beneficiaire_type']) ||
        !in_array($data['beneficiaire_type'], ['self', 'proche'], true)) {
        $errors['beneficiaire_type'] = 'Type de bénéficiaire invalide';
    }

    if (empty($data['articles']) || !is_array($data['articles'])) {
        $errors['articles'] = 'Au moins un article requis';
    }

    return $errors;
}

// Dans le controller
$errors = validateCommande($_POST);
if (!empty($errors)) {
    // Re-afficher le formulaire avec les erreurs
    require_once VIEW_PATH . '/commandes/create.php';
    exit;
}
```

## Vues — Templates PHP Natifs

```php
<!-- src/views/layouts/base.php -->
<?php if (!defined('APP_NAME')) exit; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'App Eshop', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require_once VIEW_PATH . '/layouts/navbar.php'; ?>
    <main class="container mt-4">
        <?php require_once $content; ?>
    </main>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
```

## Conventions de Nommage

| Élément | Convention | Exemple |
|---------|-----------|---------|
| Classes PHP | PascalCase | `CommandeController`, `UserModel` |
| Méthodes/fonctions | camelCase | `createCommande()`, `getById()` |
| Variables | snake_case | `$commande_id`, `$employe_nom` |
| Fichiers classes | PascalCase | `CommandeController.php` |
| Fichiers vues | kebab-case | `create-commande.php`, `login.php` |
| Tables BDD | snake_case pluriel | `commandes`, `sous_commandes` |
| Colonnes BDD | snake_case | `created_at`, `entreprise_id` |
| Constantes | SCREAMING_SNAKE | `MAX_UPLOAD_SIZE`, `CODE_LENGTH` |

## Gestion des Erreurs

```php
// Ne jamais afficher les erreurs PHP en production
// config/bootstrap.php
if ($_ENV['APP_ENV'] === 'production') {
    ini_set('display_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Exceptions BDD — toujours catcher
try {
    $commande = $commandeModel->create($data);
} catch (PDOException $e) {
    error_log('DB Error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Erreur lors de la création de la commande';
    header('Location: /commandes/new');
    exit;
}
```

## Upload de Fichiers — Documents Employés

```php
// Constantes de sécurité
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_MIME_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);
define('UPLOAD_PATH', __DIR__ . '/../storage/documents/');

function validateUpload(array $file): string|null {
    if ($file['size'] > MAX_UPLOAD_SIZE) return 'Fichier trop volumineux (max 5MB)';
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_MIME_TYPES, true)) return 'Type de fichier non autorisé';
    return null;
}

function storeDocument(array $file, int $commandeId): string {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = sprintf('%d_%s.%s', $commandeId, bin2hex(random_bytes(8)), $ext);
    move_uploaded_file($file['tmp_name'], UPLOAD_PATH . $filename);
    return $filename;
}
```

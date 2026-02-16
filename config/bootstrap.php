<?php

// --- Chargement des variables d'environnement ---
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// --- Gestion des erreurs selon l'environnement ---
if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
    ini_set('display_errors', '0');
    error_reporting(0);
} else {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// --- Démarrage de session sécurisée ---
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

// --- Constantes globales ---
define('APP_NAME',   $_ENV['APP_NAME']  ?? 'App Eshop');
define('APP_URL',    $_ENV['APP_URL']   ?? 'http://localhost:8000');
define('VIEW_PATH',  __DIR__ . '/../src/views');
define('UPLOAD_PATH', __DIR__ . '/../storage/documents/');

define('MAX_UPLOAD_SIZE',  5 * 1024 * 1024); // 5 MB
define('ALLOWED_MIME_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// --- Autoload des classes src/ ---
spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../src/models/'      . $class . '.php',
        __DIR__ . '/../src/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/database.php';

// --- Fonctions d'authentification globales ---

function requireAuth(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
}

function requireRole(string ...$roles): void
{
    requireAuth();
    if (!in_array($_SESSION['user_role'], $roles, true)) {
        http_response_code(403);
        require_once VIEW_PATH . '/errors/403.php';
        exit;
    }
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function currentUser(): array
{
    return [
        'id'    => $_SESSION['user_id']   ?? null,
        'nom'   => $_SESSION['user_nom']  ?? '',
        'role'  => $_SESSION['user_role'] ?? '',
    ];
}

// --- Token CSRF ---

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF token invalide.');
    }
}

<?php

require_once __DIR__ . '/../config/bootstrap.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Normaliser l'URI
$uri = rtrim($uri, '/') ?: '/';

// Routes publiques (sans auth)
$publicRoutes = ['GET /login', 'POST /login', 'GET /logout'];

$route = $method . ' ' . $uri;

// Vérification auth pour routes protégées
if (!in_array($route, $publicRoutes, true) && !isLoggedIn()) {
    header('Location: /login');
    exit;
}

// --- Dispatch ---
switch (true) {

    // Auth
    case $route === 'GET /login':
        (new AuthController())->showLogin();
        break;

    case $route === 'POST /login':
        (new AuthController())->login();
        break;

    case $route === 'GET /logout':
        (new AuthController())->logout();
        break;

    // Dashboard (redirige selon le rôle)
    case $route === 'GET /':
    case $route === 'GET /dashboard':
        (new DashboardController())->index();
        break;

    // Commandes
    case $route === 'GET /commandes':
        (new CommandeController())->index();
        break;

    case $route === 'GET /commandes/create':
        requireRole('employe');
        (new CommandeController())->create();
        break;

    case $route === 'POST /commandes':
        requireRole('employe');
        (new CommandeController())->store();
        break;

    case preg_match('#^GET /commandes/(\d+)$#', $uri, $m) === 1:
        (new CommandeController())->show((int) $m[1]);
        break;

    // Catalogue
    case $route === 'GET /catalogue':
        requireRole('employe');
        (new CatalogueController())->index();
        break;

    // Entreprise — validation
    case $route === 'GET /entreprise/commandes':
        requireRole('entreprise');
        (new EntrepriseController())->commandes();
        break;

    case preg_match('#^POST /entreprise/commandes/(\d+)/valider$#', $uri, $m) === 1:
        requireRole('entreprise');
        (new EntrepriseController())->valider((int) $m[1]);
        break;

    case preg_match('#^POST /entreprise/commandes/(\d+)/refuser$#', $uri, $m) === 1:
        requireRole('entreprise');
        (new EntrepriseController())->refuser((int) $m[1]);
        break;

    // Fournisseur — sous-commandes
    case $route === 'GET /fournisseur/sous-commandes':
        requireRole('fournisseur');
        (new FournisseurController())->sousCommandes();
        break;

    case preg_match('#^POST /fournisseur/sous-commandes/(\d+)/valider$#', $uri, $m) === 1:
        requireRole('fournisseur');
        (new FournisseurController())->valider((int) $m[1]);
        break;

    // Admin
    case $route === 'GET /admin':
        requireRole('admin', 'super_admin');
        (new AdminController())->index();
        break;

    // Notifications (API AJAX)
    case $route === 'GET /api/notifications':
        requireAuth();
        (new NotificationController())->list();
        break;

    case preg_match('#^POST /api/notifications/(\d+)/lire$#', $uri, $m) === 1:
        requireAuth();
        (new NotificationController())->marquerLu((int) $m[1]);
        break;

    // 404
    default:
        http_response_code(404);
        require_once VIEW_PATH . '/errors/404.php';
        break;
}

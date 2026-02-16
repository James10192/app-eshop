<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrfToken() ?>">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME, ENT_QUOTES, 'UTF-8') ?> — <?= APP_NAME ?></title>
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

    <?php require_once VIEW_PATH . '/layouts/navbar.php'; ?>

    <main class="container-fluid px-4 py-4">
        <?php require_once VIEW_PATH . '/layouts/flash.php'; ?>
        <?php require_once $content; ?>
    </main>

    <!-- Conteneur pour les modals Bootstrap (injecté dynamiquement) -->
    <div id="modal-container"></div>

    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/app.js"></script>
    <?php if (!empty($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

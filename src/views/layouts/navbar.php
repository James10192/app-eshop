<?php
$notifModel = new NotificationModel();
$notifCount = isLoggedIn() ? $notifModel->countUnread((int) $_SESSION['user_id']) : 0;
$user       = currentUser();
$role       = $user['role'] ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary app-navbar">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/dashboard">
            <i class="bi bi-shop me-1"></i><?= APP_NAME ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <?php if ($role === 'employe'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/catalogue"><i class="bi bi-grid me-1"></i>Catalogue</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/commandes"><i class="bi bi-bag me-1"></i>Mes commandes</a>
                    </li>
                <?php elseif ($role === 'entreprise'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/entreprise/commandes"><i class="bi bi-clipboard-check me-1"></i>Commandes</a>
                    </li>
                <?php elseif ($role === 'fournisseur'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/fournisseur/sous-commandes"><i class="bi bi-truck me-1"></i>Sous-commandes</a>
                    </li>
                <?php elseif (in_array($role, ['admin', 'super_admin'], true)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin"><i class="bi bi-speedometer2 me-1"></i>Administration</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav align-items-center">
                <!-- Badge notifications -->
                <li class="nav-item me-2">
                    <a class="nav-link position-relative" href="#" id="btn-notifications" title="Notifications">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <?php if ($notifCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $notifCount ?>
                                <span class="visually-hidden">notifications non lues</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Menu utilisateur -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($user['prenom'] ?? $user['nom'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role)), ENT_QUOTES, 'UTF-8') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

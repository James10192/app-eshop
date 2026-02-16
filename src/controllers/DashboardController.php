<?php

class DashboardController
{
    public function index(): void
    {
        requireAuth();

        $role = $_SESSION['user_role'];

        switch ($role) {
            case 'employe':
                header('Location: /commandes');
                break;
            case 'entreprise':
                header('Location: /entreprise/commandes');
                break;
            case 'fournisseur':
                header('Location: /fournisseur/sous-commandes');
                break;
            case 'admin':
            case 'super_admin':
                header('Location: /admin');
                break;
            default:
                header('Location: /login');
        }
        exit;
    }
}

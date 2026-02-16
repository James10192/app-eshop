<?php

class AuthController
{
    public function showLogin(): void
    {
        if (isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        $pageTitle = 'Connexion';
        require_once VIEW_PATH . '/auth/login.php';
    }

    public function login(): void
    {
        verifyCsrf();

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Email et mot de passe requis.';
            header('Location: /login');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['flash_error'] = 'Identifiants incorrects.';
            header('Location: /login');
            exit;
        }

        // Régénérer l'ID de session (prévention fixation)
        session_regenerate_id(true);

        $_SESSION['user_id']             = $user['id'];
        $_SESSION['user_nom']            = $user['nom'];
        $_SESSION['user_prenom']         = $user['prenom'];
        $_SESSION['user_role']           = $user['role'];
        $_SESSION['user_entreprise_id']  = $user['entreprise_id'];
        $_SESSION['user_fournisseur_id'] = $user['fournisseur_id'];

        header('Location: /dashboard');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}

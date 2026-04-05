<?php

namespace App\Framework;

class Controller
{
    /**
     * Determine whether an authenticated user exists in the session.
     */
    protected function isAuthenticated(): bool
    {
        return !empty($_SESSION['auth_user_id']) && !empty($_SESSION['auth_user_role']);
    }

    /**
     * Return the current authenticated user role from session state.
     */
    protected function authUserRole(): string
    {
        return (string) ($_SESSION['auth_user_role'] ?? '');
    }

    /**
     * Require authentication or redirect to the provided path.
     */
    protected function requireAuthentication(string $redirectPath = '/signin'): void
    {
        if ($this->isAuthenticated()) {
            return;
        }

        $this->redirect($redirectPath);
    }

    /**
     * Require one of the allowed roles or redirect when unauthorized.
     */
    protected function requireRole(array $allowedRoles, string $redirectPath = '/dashboard'): void
    {
        $this->requireAuthentication();

        if (in_array($this->authUserRole(), $allowedRoles, true)) {
            return;
        }

        $this->redirect($redirectPath);
    }

    /**
     * Redirect the current request to a new path.
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /**
     * Clear the active session and remove its cookie.
     */
    protected function destroySession(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $cookieParams = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookieParams['path'],
                $cookieParams['domain'],
                $cookieParams['secure'],
                $cookieParams['httponly']
            );
        }

        session_destroy();
    }
}

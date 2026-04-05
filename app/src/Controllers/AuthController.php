<?php

namespace App\Controllers;

use App\Config;
use App\Enums\RoleType;
use App\Repositories\UserRepository;
use App\Services\SignupService;
use App\ViewModels\AuthViewModel;
use Throwable;

class AuthController
{
    public function showSigninForm(array $params = []): void
    {
        if (!empty($_SESSION['auth_user_id']) && !empty($_SESSION['auth_user_role'])) {
            header('Location: /dashboard');
            exit;
        }

        $signupSuccessMessage = $_SESSION['signup_success_message'] ?? ($_SESSION['password_reset_success_message'] ?? null);

        unset($_SESSION['signup_success_message']);
        unset($_SESSION['password_reset_success_message']);

        $viewModel = AuthViewModel::createForSignin($signupSuccessMessage);

        include __DIR__ . '/../Views/auth/signin.php';
    }

    public function showResetPasswordForm(array $params = []): void
    {
        if (empty($_SESSION['password_reset_user_id'])) {
            header('Location: /forget-password');
            exit;
        }

        $viewModel = AuthViewModel::createForResetPassword([
            'username' => (string) ($_SESSION['password_reset_username'] ?? ''),
        ]);

        include __DIR__ . '/../Views/auth/resetPassword.php';
    }

    public function showSignupForm(array $params = []): void
    {
        $viewModel = AuthViewModel::createForSignup();

        include __DIR__ . '/../Views/auth/signup.php';
    }

    public function handleSigninForm(array $params = []): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $errors = [];

        if ($username === '') {
            $errors['username'] = 'Username is required.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($errors !== []) {
            $viewModel = AuthViewModel::createForSignin(
                null,
                $errors,
                ['username' => $username]
            );

            include __DIR__ . '/../Views/auth/signin.php';
            return;
        }

        try {
            $userRepository = new UserRepository(Config::pdo());
            $user = $userRepository->findByUsername($username);
        } catch (Throwable $exception) {
            $viewModel = AuthViewModel::createForSignin(
                null,
                ['general' => 'Unable to sign in right now. Please try again later.'],
                ['username' => $username]
            );

            include __DIR__ . '/../Views/auth/signin.php';
            return;
        }

        if ($user === null || !password_verify($password, $user->getPassword())) {
            $viewModel = AuthViewModel::createForSignin(
                null,
                ['general' => 'Invalid username or password.'],
                ['username' => $username]
            );

            include __DIR__ . '/../Views/auth/signin.php';
            return;
        }

        $_SESSION['auth_user_id'] = $user->getUserId();
        $_SESSION['auth_user_role'] = $user->getRole();
        $_SESSION['auth_user_name'] = $user->getName();
        session_regenerate_id(true);

        if (!RoleType::isValid((string) $_SESSION['auth_user_role'])) {
            session_unset();
            session_destroy();

            $viewModel = AuthViewModel::createForSignin(
                null,
                ['general' => 'Your account role is not supported for login.'],
                ['username' => $username]
            );

            include __DIR__ . '/../Views/auth/signin.php';
            return;
        }

        header('Location: /dashboard');
        exit;
    }

    public function handleSignupForm(array $params = []): void
    {
        try {
            $service = new SignupService(Config::pdo());
            $result = $service->register($_POST);
        } catch (Throwable $exception) {
            $result = [
                'success' => false,
                'errors' => [
                    'general' => 'Unable to connect to the database. Please try again later.',
                ],
                'input' => $_POST,
            ];
        }

        if (($result['success'] ?? false) === true) {
            $_SESSION['signup_success_message'] = 'Your account has been created. Please log in to continue.';

            header('Location: /signin');
            exit;
        }

        $viewModel = AuthViewModel::createForSignup(
            $result['input'] ?? [],
            $result['errors'] ?? []
        );

        include __DIR__ . '/../Views/auth/signup.php';
    }

    public function showForgetPasswordForm(array $params = []): void
    {
        $viewModel = AuthViewModel::createForForgetPassword();

        include __DIR__ . '/../Views/auth/forgetPassword.php';
    }

    public function handleForgetPasswordForm(array $params = []): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $kvkNr = trim((string) ($_POST['kvkNr'] ?? ''));

        $errors = [];

        if ($username === '') {
            $errors['username'] = 'Username is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($kvkNr === '') {
            $errors['kvkNr'] = 'Chamber of Commerce number is required.';
        } elseif (!preg_match('/^\d{8}$/', $kvkNr)) {
            $errors['kvkNr'] = 'Chamber of Commerce number must contain exactly 8 digits.';
        }

        if ($errors !== []) {
            $viewModel = AuthViewModel::createForForgetPassword(
                ['username' => $username, 'email' => $email, 'kvkNr' => $kvkNr],
                $errors
            );

            include __DIR__ . '/../Views/auth/forgetPassword.php';
            return;
        }

        try {
            $userRepository = new UserRepository(Config::pdo());
            $user = $userRepository->findByUsername($username);
        } catch (Throwable $exception) {
            $viewModel = AuthViewModel::createForForgetPassword(
                ['username' => $username, 'email' => $email, 'kvkNr' => $kvkNr],
                ['general' => 'Unable to verify your account right now. Please try again later.']
            );

            include __DIR__ . '/../Views/auth/forgetPassword.php';
            return;
        }

        if (
            $user === null
            || strtolower($user->getEmail()) !== $email
            || (string) $user->getKvkNr() !== $kvkNr
        ) {
            $viewModel = AuthViewModel::createForForgetPassword(
                ['username' => $username, 'email' => $email, 'kvkNr' => $kvkNr],
                ['general' => 'The username, email, and Chamber of Commerce number combination does not match any account.']
            );

            include __DIR__ . '/../Views/auth/forgetPassword.php';
            return;
        }

        $_SESSION['password_reset_user_id'] = $user->getUserId();
        $_SESSION['password_reset_username'] = $user->getUsername();

        header('Location: /reset-password');
        exit;
    }

    public function handleResetPasswordForm(array $params = []): void
    {
        $resetUserId = (int) ($_SESSION['password_reset_user_id'] ?? 0);
        $resetUsername = (string) ($_SESSION['password_reset_username'] ?? '');

        if ($resetUserId <= 0) {
            header('Location: /forget-password');
            exit;
        }

        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirmPassword'] ?? '');

        $errors = [];

        if ($password === '') {
            $errors['password'] = 'New password is required.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }

        if ($confirmPassword === '') {
            $errors['confirmPassword'] = 'Please confirm your new password.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirmPassword'] = 'Passwords do not match.';
        }

        if ($errors !== []) {
            $viewModel = AuthViewModel::createForResetPassword(
                ['username' => $resetUsername],
                $errors
            );

            include __DIR__ . '/../Views/auth/resetPassword.php';
            return;
        }

        try {
            $userRepository = new UserRepository(Config::pdo());
            $updated = $userRepository->updatePassword($resetUserId, password_hash($password, PASSWORD_DEFAULT));
        } catch (Throwable $exception) {
            $updated = false;
        }

        if (!$updated) {
            $viewModel = AuthViewModel::createForResetPassword(
                ['username' => $resetUsername],
                ['general' => 'Unable to reset password right now. Please try again later.']
            );

            include __DIR__ . '/../Views/auth/resetPassword.php';
            return;
        }

        unset($_SESSION['password_reset_user_id'], $_SESSION['password_reset_username']);
        $_SESSION['password_reset_success_message'] = 'Password updated successfully. Please sign in.';

        header('Location: /signin');
        exit;
    }

    public function handleSignout(array $params = []): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header('Location: /signin');
        exit;
    }
}
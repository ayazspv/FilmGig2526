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

        $signupSuccessMessage = $_SESSION['signup_success_message'] ?? null;

        unset($_SESSION['signup_success_message']);

        $viewModel = AuthViewModel::createForSignin($signupSuccessMessage);

        include __DIR__ . '/../Views/auth/signin.php';
    }

    public function showResetPasswordForm(array $params = []): void
    {
        $viewModel = AuthViewModel::createForResetPassword();

        include __DIR__ . '/../Views/auth/resetPassword.php';
    }

    public function showSignupForm(array $params = []): void
    {
        $viewModel = AuthViewModel::createForSignup();

        include __DIR__ . '/../Views/auth/signup.php';
    }

    public function handleSigninForm(array $params = []): void
    {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');

        $errors = [];

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($errors !== []) {
            $viewModel = AuthViewModel::createForSignin(
                null,
                $errors,
                ['email' => $email]
            );

            include __DIR__ . '/../Views/auth/signin.php';
            return;
        }

        try {
            $userRepository = new UserRepository(Config::pdo());
            $user = $userRepository->findByEmail($email);
        } catch (Throwable $exception) {
            $viewModel = AuthViewModel::createForSignin(
                null,
                ['general' => 'Unable to sign in right now. Please try again later.'],
                ['email' => $email]
            );

            include __DIR__ . '/../Views/auth/signin.php';
            return;
        }

        if ($user === null || !password_verify($password, $user->getPassword())) {
            $viewModel = AuthViewModel::createForSignin(
                null,
                ['general' => 'Invalid email or password.'],
                ['email' => $email]
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
                ['email' => $email]
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
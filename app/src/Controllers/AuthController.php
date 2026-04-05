<?php

namespace App\Controllers;

use App\Config;
use App\Services\SignupService;
use App\ViewModels\AuthViewModel;
use Throwable;

class AuthController
{
    public function showSigninForm(array $params = []): void
    {
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
}
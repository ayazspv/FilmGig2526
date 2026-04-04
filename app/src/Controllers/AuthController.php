<?php

namespace App\Controllers;

use App\ViewModels\AuthViewModel;

class AuthController
{
    public function showSigninForm(array $params = []): void
    {
        $viewModel = AuthViewModel::createForSignin();

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

    public function showForgetPasswordForm(array $params = []): void
    {
        $viewModel = AuthViewModel::createForForgetPassword();

        include __DIR__ . '/../Views/auth/forgetPassword.php';
    }
}
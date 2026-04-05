<?php

namespace App\Controllers;

use App\Config;
use App\Framework\Controller;
use App\Services\PasswordResetService;
use App\Services\SigninService;
use App\Services\SignupService;
use App\ViewModels\AuthViewModel;
use Throwable;

class AuthController extends Controller
{
    /**
     * Display the signin page and one-time success messages.
     */
    public function showSigninForm(array $params = []): void
    {
        if (!empty($_SESSION['auth_user_id']) && !empty($_SESSION['auth_user_role'])) {
            $this->redirect('/dashboard');
        }

        $signupSuccessMessage = $_SESSION['signup_success_message'] ?? ($_SESSION['password_reset_success_message'] ?? null);

        unset($_SESSION['signup_success_message']);
        unset($_SESSION['password_reset_success_message']);

        $this->renderSignin($signupSuccessMessage);
    }

    /**
     * Display the reset password page after identity verification.
     */
    public function showResetPasswordForm(array $params = []): void
    {
        if (empty($_SESSION['password_reset_user_id'])) {
            $this->redirect('/forget-password');
        }

        $this->renderResetPassword([
            'username' => (string) ($_SESSION['password_reset_username'] ?? ''),
        ]);
    }

    /**
     * Display the signup page.
     */
    public function showSignupForm(array $params = []): void
    {
        $this->renderSignup();
    }

    /**
     * Authenticate signin credentials and start an authenticated session.
     */
    public function handleSigninForm(array $params = []): void
    {
        $service = new SigninService(Config::pdo());
        $result = $service->authenticate($_POST);

        if (($result['success'] ?? false) !== true) {
            $this->renderSignin(
                null,
                $result['errors'] ?? ['general' => 'Unable to sign in right now. Please try again later.'],
                $result['input'] ?? []
            );
            return;
        }

        /** @var \App\Models\User $user */
        $user = $result['user'];

        $_SESSION['auth_user_id'] = $user->getUserId();
        $_SESSION['auth_user_role'] = $user->getRole();
        $_SESSION['auth_user_name'] = $user->getName();
        session_regenerate_id(true);

        $this->redirect('/dashboard');
    }

    /**
     * Handle signup form submission and account creation.
     */
    public function handleSignupForm(array $params = []): void
    {
        $result = $this->attemptSignupRegistration($_POST);

        if (($result['success'] ?? false) === true) {
            $this->storeSignupSuccessMessage();

            $this->redirect('/signin');
        }

        $this->renderSignup(
            $result['input'] ?? [],
            $result['errors'] ?? []
        );
    }

    /**
     * Register a new user and normalize failure handling.
     */
    private function attemptSignupRegistration(array $input): array
    {
        try {
            return (new SignupService(Config::pdo()))->register($input);
        } catch (Throwable $exception) {
            return $this->buildSignupConnectionFailure($input);
        }
    }

    /**
     * Build a consistent signup failure result for connection errors.
     */
    private function buildSignupConnectionFailure(array $input): array
    {
        return [
            'success' => false,
            'errors' => [
                'general' => 'Unable to connect to the database. Please try again later.',
            ],
            'input' => $input,
        ];
    }

    /**
     * Store the one-time success message shown on signin.
     */
    private function storeSignupSuccessMessage(): void
    {
        $_SESSION['signup_success_message'] = 'Your account has been created. Please log in to continue.';
    }

    /**
     * Display the forgot-password identity verification page.
     */
    public function showForgetPasswordForm(array $params = []): void
    {
        $this->renderForgetPassword();
    }

    /**
     * Verify user identity before allowing password reset.
     */
    public function handleForgetPasswordForm(array $params = []): void
    {
        $service = new PasswordResetService(Config::pdo());
        $result = $service->verifyIdentity($_POST);

        if (($result['success'] ?? false) !== true) {
            $this->renderForgetPassword(
                $result['input'] ?? [],
                $result['errors'] ?? ['general' => 'Unable to verify your account right now. Please try again later.']
            );
            return;
        }

        $_SESSION['password_reset_user_id'] = (int) $result['userId'];
        $_SESSION['password_reset_username'] = (string) $result['username'];

        $this->redirect('/reset-password');
    }

    /**
     * Process password reset for a previously verified user.
     */
    public function handleResetPasswordForm(array $params = []): void
    {
        $resetUserId = (int) ($_SESSION['password_reset_user_id'] ?? 0);
        $resetUsername = (string) ($_SESSION['password_reset_username'] ?? '');

        if ($resetUserId <= 0) {
            $this->redirect('/forget-password');
        }

        $service = new PasswordResetService(Config::pdo());
        $result = $service->resetPassword($resetUserId, $_POST);

        if (($result['success'] ?? false) !== true) {
            $this->renderResetPassword(
                ['username' => $resetUsername],
                $result['errors'] ?? ['general' => 'Unable to reset password right now. Please try again later.']
            );
            return;
        }

        unset($_SESSION['password_reset_user_id'], $_SESSION['password_reset_username']);
        $_SESSION['password_reset_success_message'] = 'Password updated successfully. Please sign in.';

        $this->redirect('/signin');
    }

    /**
     * Sign out the current user and clear session state.
     */
    public function handleSignout(array $params = []): void
    {
        $this->destroySession();

        $this->redirect('/signin');
    }

    /**
     * Render the signin view with optional messages and form data.
     */
    private function renderSignin(?string $successMessage = null, array $errors = [], array $oldInput = []): void
    {
        $viewModel = AuthViewModel::createForSignin($successMessage, $errors, $oldInput);
        include __DIR__ . '/../Views/auth/signin.php';
    }

    /**
     * Render the signup view with validation feedback.
     */
    private function renderSignup(array $oldInput = [], array $errors = []): void
    {
        $viewModel = AuthViewModel::createForSignup($oldInput, $errors);
        include __DIR__ . '/../Views/auth/signup.php';
    }

    /**
     * Render the forgot-password view with validation feedback.
     */
    private function renderForgetPassword(array $oldInput = [], array $errors = []): void
    {
        $viewModel = AuthViewModel::createForForgetPassword($oldInput, $errors);
        include __DIR__ . '/../Views/auth/forgetPassword.php';
    }

    /**
     * Render the reset-password view with validation feedback.
     */
    private function renderResetPassword(array $oldInput = [], array $errors = []): void
    {
        $viewModel = AuthViewModel::createForResetPassword($oldInput, $errors);
        include __DIR__ . '/../Views/auth/resetPassword.php';
    }
}
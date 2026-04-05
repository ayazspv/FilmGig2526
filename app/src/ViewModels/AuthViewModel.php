<?php

namespace App\ViewModels;

class AuthViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly array $oldInput = [],
        public readonly array $errors = [],
        public readonly ?string $successMessage = null,
        public readonly array $roleOptions = [],
    ) {
    }

    public static function createForSignin(?string $successMessage = null): self
    {
        return new self(
            pageTitle: 'Sign In - FilmGig',
            successMessage: $successMessage,
        );
    }

    public static function createForResetPassword(): self
    {
        return new self(
            pageTitle: 'Reset Password - FilmGig',
        );
    }

    public static function createForSignup(array $oldInput = [], array $errors = [], ?string $successMessage = null): self
    {
        return new self(
            pageTitle: 'Sign Up - FilmGig',
            oldInput: $oldInput,
            errors: $errors,
            successMessage: $successMessage,
            roleOptions: [
                ['value' => 'productionHouse', 'label' => 'Production Company'],
                ['value' => 'freelance', 'label' => 'Freelancer'],
            ],
        );
    }

    public static function createForForgetPassword(): self
    {
        return new self(
            pageTitle: 'Forgot Password - FilmGig',
        );
    }
}

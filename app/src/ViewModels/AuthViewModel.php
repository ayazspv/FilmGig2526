<?php

namespace App\ViewModels;

use App\Enums\RoleType;

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

    public static function createForSignin(?string $successMessage = null, array $errors = [], array $oldInput = []): self
    {
        return new self(
            pageTitle: 'Sign In - FilmGig',
            successMessage: $successMessage,
            errors: $errors,
            oldInput: $oldInput,
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
                ['value' => RoleType::PRODUCTION_HOUSE->value, 'label' => RoleType::PRODUCTION_HOUSE->label()],
                ['value' => RoleType::FREELANCER->value, 'label' => RoleType::FREELANCER->label()],
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

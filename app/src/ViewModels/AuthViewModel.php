<?php

namespace App\ViewModels;

class AuthViewModel
{
    public static function createForSignin(): self
    {
        return new self();
    }

    public static function createForResetPassword(): self
    {
        return new self();
    }

    public static function createForSignup(): self
    {
        return new self();
    }

    public static function createForForgetPassword(): self
    {
        return new self();
    }
}

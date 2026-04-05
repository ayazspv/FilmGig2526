<?php

namespace App\Services\Interfaces;

interface ISignupService
{
    /**
     * Register a new account and create its role record.
     */
    public function register(array $input): array;
}

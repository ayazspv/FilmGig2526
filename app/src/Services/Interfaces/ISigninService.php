<?php

namespace App\Services\Interfaces;

interface ISigninService
{
    /**
     * Authenticate a username/password pair.
     */
    public function authenticate(array $input): array;
}

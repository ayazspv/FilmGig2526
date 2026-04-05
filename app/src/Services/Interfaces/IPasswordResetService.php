<?php

namespace App\Services\Interfaces;

interface IPasswordResetService
{
    /**
     * Verify a user identity using username, email, and KVK.
     */
    public function verifyIdentity(array $input): array;

    /**
     * Update the password for a verified user.
     */
    public function resetPassword(int $userId, array $input): array;
}

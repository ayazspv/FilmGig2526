<?php

namespace App\Services;

use App\Framework\Service;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IPasswordResetService;
use PDO;
use Throwable;

class PasswordResetService extends Service implements IPasswordResetService
{
    private UserRepository $userRepository;

    /**
     * Build the password reset service with its user repository.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userRepository = new UserRepository($pdo);
    }

    /**
     * Verify username, email, and KVK before allowing a reset.
     */
    public function verifyIdentity(array $input): array
    {
        $normalizedInput = $this->normalizeIdentityInput($input);
        $errors = $this->validateIdentityInput($normalizedInput);

        if ($errors !== []) {
            return $this->buildFailureResult($errors, $normalizedInput);
        }

        $user = $this->findUserByUsername($normalizedInput['username']);

        if ($user === null || !$this->matchesIdentity($user, $normalizedInput)) {
            return $this->buildIdentityFailureResult($user, $normalizedInput);
        }

        return $this->buildVerifySuccessResult($user->getUserId(), $user->getUsername(), $normalizedInput);
    }

    /**
     * Update the password for a verified user.
     */
    public function resetPassword(int $userId, array $input): array
    {
        $normalizedInput = $this->normalizePasswordInput($input);
        $errors = $this->validatePasswordInput($normalizedInput);

        if ($errors !== []) {
            return $this->buildFailureResult($errors, $normalizedInput);
        }

        $updated = $this->updatePassword($userId, $normalizedInput['password']);

        if (!$updated) {
            return $this->buildFailureResult(
                ['general' => 'Unable to reset password right now. Please try again later.'],
                $normalizedInput
            );
        }

        return ['success' => true];
    }

    /**
     * Normalize the identity verification input.
     */
    private function normalizeIdentityInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'email' => strtolower(trim((string) ($input['email'] ?? ''))),
            'kvkNr' => trim((string) ($input['kvkNr'] ?? '')),
        ];
    }

    /**
     * Normalize the new password input.
     */
    private function normalizePasswordInput(array $input): array
    {
        return [
            'password' => (string) ($input['password'] ?? ''),
            'confirmPassword' => (string) ($input['confirmPassword'] ?? ''),
        ];
    }

    /**
     * Validate the identity verification fields.
     */
    private function validateIdentityInput(array $input): array
    {
        $errors = [];

        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
        }

        if ($input['email'] === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($input['kvkNr'] === '') {
            $errors['kvkNr'] = 'Chamber of Commerce number is required.';
        } elseif (!preg_match('/^\d{8}$/', $input['kvkNr'])) {
            $errors['kvkNr'] = 'Chamber of Commerce number must contain exactly 8 digits.';
        }

        return $errors;
    }

    /**
     * Validate the new password fields.
     */
    private function validatePasswordInput(array $input): array
    {
        $errors = [];

        if ($input['password'] === '') {
            $errors['password'] = 'New password is required.';
        } elseif (strlen($input['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }

        if ($input['confirmPassword'] === '') {
            $errors['confirmPassword'] = 'Please confirm your new password.';
        } elseif ($input['password'] !== $input['confirmPassword']) {
            $errors['confirmPassword'] = 'Passwords do not match.';
        }

        return $errors;
    }

    /**
     * Return a standardized success payload for identity verification.
     */
    private function buildVerifySuccessResult(int $userId, string $username, array $input): array
    {
        return [
            'success' => true,
            'userId' => $userId,
            'username' => $username,
            'input' => $input,
        ];
    }

    /**
     * Check whether user data matches identity input.
     */
    private function matchesIdentity(object $user, array $input): bool
    {
        return strtolower($user->getEmail()) === $input['email']
            && (string) $user->getKvkNr() === $input['kvkNr'];
    }

    /**
     * Build the correct failure payload for identity verification.
     */
    private function buildIdentityFailureResult(?object $user, array $input): array
    {
        if ($user === null) {
            return $this->buildFailureResult(
                ['general' => 'Unable to verify your account right now. Please try again later.'],
                $input
            );
        }

        return $this->buildFailureResult(
            ['general' => 'The username, email, and Chamber of Commerce number combination does not match any account.'],
            $input
        );
    }

    /**
     * Look up a user by username and suppress repository exceptions.
     */
    private function findUserByUsername(string $username)
    {
        try {
            return $this->userRepository->findByUsername($username);
        } catch (Throwable $exception) {
            return null;
        }
    }

    /**
     * Update the stored password hash for a user.
     */
    private function updatePassword(int $userId, string $password): bool
    {
        try {
            return $this->userRepository->updatePassword(
                $userId,
                password_hash($password, PASSWORD_DEFAULT)
            );
        } catch (Throwable $exception) {
            return false;
        }
    }
}

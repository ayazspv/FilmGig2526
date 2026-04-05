<?php

namespace App\Services;

use App\Repositories\UserRepository;
use PDO;
use Throwable;

class PasswordResetService
{
    private UserRepository $userRepository;

    public function __construct(PDO $pdo)
    {
        $this->userRepository = new UserRepository($pdo);
    }

    public function verifyIdentity(array $input): array
    {
        $normalizedInput = $this->normalizeIdentityInput($input);
        $errors = $this->validateIdentityInput($normalizedInput);

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors,
                'input' => $normalizedInput,
            ];
        }

        try {
            $user = $this->userRepository->findByUsername($normalizedInput['username']);
        } catch (Throwable $exception) {
            return [
                'success' => false,
                'errors' => ['general' => 'Unable to verify your account right now. Please try again later.'],
                'input' => $normalizedInput,
            ];
        }

        if (
            $user === null
            || strtolower($user->getEmail()) !== $normalizedInput['email']
            || (string) $user->getKvkNr() !== $normalizedInput['kvkNr']
        ) {
            return [
                'success' => false,
                'errors' => ['general' => 'The username, email, and Chamber of Commerce number combination does not match any account.'],
                'input' => $normalizedInput,
            ];
        }

        return [
            'success' => true,
            'userId' => $user->getUserId(),
            'username' => $user->getUsername(),
            'input' => $normalizedInput,
        ];
    }

    public function resetPassword(int $userId, array $input): array
    {
        $normalizedInput = $this->normalizePasswordInput($input);
        $errors = $this->validatePasswordInput($normalizedInput);

        if ($errors !== []) {
            return [
                'success' => false,
                'errors' => $errors,
                'input' => $normalizedInput,
            ];
        }

        try {
            $updated = $this->userRepository->updatePassword(
                $userId,
                password_hash($normalizedInput['password'], PASSWORD_DEFAULT)
            );
        } catch (Throwable $exception) {
            $updated = false;
        }

        if (!$updated) {
            return [
                'success' => false,
                'errors' => ['general' => 'Unable to reset password right now. Please try again later.'],
                'input' => $normalizedInput,
            ];
        }

        return ['success' => true];
    }

    private function normalizeIdentityInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'email' => strtolower(trim((string) ($input['email'] ?? ''))),
            'kvkNr' => trim((string) ($input['kvkNr'] ?? '')),
        ];
    }

    private function normalizePasswordInput(array $input): array
    {
        return [
            'password' => (string) ($input['password'] ?? ''),
            'confirmPassword' => (string) ($input['confirmPassword'] ?? ''),
        ];
    }

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
}

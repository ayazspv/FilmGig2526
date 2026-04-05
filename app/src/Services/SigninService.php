<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Repositories\UserRepository;
use PDO;
use Throwable;

class SigninService
{
    private UserRepository $userRepository;

    public function __construct(PDO $pdo)
    {
        $this->userRepository = new UserRepository($pdo);
    }

    public function authenticate(array $input): array
    {
        $normalizedInput = $this->normalizeInput($input);
        $errors = $this->validate($normalizedInput);

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
                'errors' => ['general' => 'Unable to sign in right now. Please try again later.'],
                'input' => $normalizedInput,
            ];
        }

        if ($user === null || !password_verify($normalizedInput['password'], $user->getPassword())) {
            return [
                'success' => false,
                'errors' => ['general' => 'Invalid username or password.'],
                'input' => $normalizedInput,
            ];
        }

        if (!RoleType::isValid($user->getRole())) {
            return [
                'success' => false,
                'errors' => ['general' => 'Your account role is not supported for login.'],
                'input' => $normalizedInput,
            ];
        }

        return [
            'success' => true,
            'user' => $user,
            'input' => $normalizedInput,
        ];
    }

    private function normalizeInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'password' => (string) ($input['password'] ?? ''),
        ];
    }

    private function validate(array $input): array
    {
        $errors = [];

        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
        }

        if ($input['password'] === '') {
            $errors['password'] = 'Password is required.';
        }

        return $errors;
    }
}

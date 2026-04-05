<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Framework\Service;
use App\Repositories\UserRepository;
use App\Services\Interfaces\ISigninService;
use PDO;
use Throwable;

class SigninService extends Service implements ISigninService
{
    private UserRepository $userRepository;

    /**
     * Build the signin service with its user repository.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userRepository = new UserRepository($pdo);
    }

    /**
     * Authenticate a username and password pair.
     */
    public function authenticate(array $input): array
    {
        $normalizedInput = $this->normalizeInput($input);
        $errors = $this->validate($normalizedInput);

        if ($errors !== []) {
            return $this->buildFailureResult($errors, $normalizedInput);
        }

        $user = $this->findUserByUsername($normalizedInput['username']);

        if ($user === null) {
            return $this->buildRepositoryFailureResult($normalizedInput);
        }

        if (!$this->isValidPassword($normalizedInput['password'], $user->getPassword())) {
            return $this->buildAuthenticationFailureResult($normalizedInput);
        }

        if (!$this->hasSupportedRole($user->getRole())) {
            return $this->buildUnsupportedRoleFailureResult($normalizedInput);
        }

        return $this->buildSuccessResult($user, $normalizedInput);
    }

    /**
     * Normalize signin input into a predictable shape.
     */
    private function normalizeInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'password' => (string) ($input['password'] ?? ''),
        ];
    }

    /**
     * Validate the required signin fields.
     */
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

    /**
     * Return the failure payload used when the repository cannot be reached.
     */
    private function buildRepositoryFailureResult(array $input): array
    {
        return $this->buildFailureResult(
            ['general' => 'Unable to sign in right now. Please try again later.'],
            $input
        );
    }

    /**
     * Return the failure payload used for invalid credentials.
     */
    private function buildAuthenticationFailureResult(array $input): array
    {
        return $this->buildFailureResult(
            ['general' => 'Invalid username or password.'],
            $input
        );
    }

    /**
     * Return the failure payload used when the role is unsupported.
     */
    private function buildUnsupportedRoleFailureResult(array $input): array
    {
        return $this->buildFailureResult(
            ['general' => 'Your account role is not supported for login.'],
            $input
        );
    }

    /**
     * Return a standardized success payload.
     */
    private function buildSuccessResult(object $user, array $input): array
    {
        return [
            'success' => true,
            'user' => $user,
            'input' => $input,
        ];
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
     * Verify the supplied password against the stored password hash.
     */
    private function isValidPassword(string $password, string $passwordHash): bool
    {
        return password_verify($password, $passwordHash);
    }

    /**
     * Check whether the user's role is supported for signin.
     */
    private function hasSupportedRole(string $role): bool
    {
        return RoleType::isValid($role);
    }
}

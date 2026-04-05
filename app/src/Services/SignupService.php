<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Framework\Service;
use App\Repositories\FreelancerRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\ISignupService;
use PDO;
use Throwable;

class SignupService extends Service implements ISignupService
{
    private UserRepository $userRepository;
    private ProductionHouseRepository $productionHouseRepository;
    private FreelancerRepository $freelancerRepository;

    /**
     * Build the signup service with the repositories it needs.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userRepository = new UserRepository($pdo);
        $this->productionHouseRepository = new ProductionHouseRepository($pdo);
        $this->freelancerRepository = new FreelancerRepository($pdo);
    }

    /**
     * Register a new user and create the matching role record.
     */
    public function register(array $input): array
    {
        $normalizedInput = $this->normalizeInput($input);
        $errors = $this->validate($normalizedInput);

        if ($errors !== []) {
            return $this->buildFailureResult($errors, $normalizedInput);
        }

        try {
            return $this->registerInTransaction($normalizedInput);
        } catch (Throwable $exception) {
            return $this->buildFailureResult(
                ['general' => 'Unable to create your account right now. Please try again.'],
                $normalizedInput
            );
        }
    }

    /**
     * Normalize signup input into a predictable array.
     */
    private function normalizeInput(array $input): array
    {
        $value = static function (array $source, string $key, string $default = ''): string {
            $item = $source[$key] ?? $default;

            return is_string($item) ? trim($item) : $default;
        };

        return [
            'username' => $value($input, 'username'),
            'name' => $value($input, 'name'),
            'email' => strtolower($value($input, 'email')),
            'password' => $value($input, 'password'),
            'role' => $value($input, 'role'),
            'address' => $value($input, 'address'),
            'bio' => $value($input, 'bio'),
            'kvkNr' => $value($input, 'kvkNr'),
            'companyName' => $value($input, 'companyName'),
            'website' => $value($input, 'website'),
            'dateOfBirth' => $value($input, 'dateOfBirth'),
        ];
    }

    /**
     * Validate all signup fields and role-specific requirements.
     */
    private function validate(array $input): array
    {
        $errors = [];

        $this->validateUsername($input, $errors);
        $this->validateName($input, $errors);
        $this->validateEmail($input, $errors);
        $this->validatePassword($input, $errors);
        $this->validateRole($input, $errors);
        $this->validateKvkNr($input, $errors);
        $this->validateRoleSpecificFields($input, $errors);

        return $errors;
    }

    /**
     * Validate username presence and uniqueness.
     */
    private function validateUsername(array $input, array &$errors): void
    {
        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
            return;
        }

        if (preg_match('/^[A-Za-z0-9.]+$/', $input['username']) !== 1) {
            $errors['username'] = 'Username may only contain letters, numbers, and dots (.).';
            return;
        }

        if ($this->userRepository->usernameExists($input['username'])) {
            $errors['username'] = 'This username is already taken.';
        }
    }

    /**
     * Validate the full name field.
     */
    private function validateName(array $input, array &$errors): void
    {
        if ($input['name'] === '') {
            $errors['name'] = 'Full name is required.';
        }
    }

    /**
     * Validate email presence, format, and uniqueness.
     */
    private function validateEmail(array $input, array &$errors): void
    {
        if ($input['email'] === '') {
            $errors['email'] = 'Email address is required.';
            return;
        }

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
            return;
        }

        if ($this->userRepository->emailExists($input['email'])) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    /**
     * Validate password presence and minimum length.
     */
    private function validatePassword(array $input, array &$errors): void
    {
        if ($input['password'] === '') {
            $errors['password'] = 'Password is required.';
            return;
        }

        if (strlen($input['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }
    }

    /**
     * Validate that the chosen role is allowed.
     */
    private function validateRole(array $input, array &$errors): void
    {
        if (!RoleType::isValid($input['role']) || $input['role'] === RoleType::ADMIN->value) {
            $errors['role'] = 'Please choose a valid account type.';
        }
    }

    /**
     * Validate the KVK number presence, format, and uniqueness.
     */
    private function validateKvkNr(array $input, array &$errors): void
    {
        if ($input['kvkNr'] === '') {
            $errors['kvkNr'] = 'Chamber of Commerce number is required.';
            return;
        }

        if (!preg_match('/^\d{8}$/', $input['kvkNr'])) {
            $errors['kvkNr'] = 'Chamber of Commerce number must contain exactly 8 digits.';
            return;
        }

        if ($this->userRepository->kvkNrExists($input['kvkNr'])) {
            $errors['kvkNr'] = 'This Chamber of Commerce number is already registered.';
        }
    }

    /**
     * Validate the fields that depend on the selected role.
     */
    private function validateRoleSpecificFields(array $input, array &$errors): void
    {
        if ($input['role'] === RoleType::PRODUCTION_HOUSE->value) {
            $this->validateProductionHouseFields($input, $errors);
            return;
        }

        if ($input['role'] === RoleType::FREELANCER->value) {
            $this->validateFreelancerFields($input, $errors);
        }
    }

    /**
     * Validate production house only fields.
     */
    private function validateProductionHouseFields(array $input, array &$errors): void
    {
        if ($input['companyName'] === '') {
            $errors['companyName'] = 'Company name is required for production companies.';
        }

        if ($input['website'] !== '' && !filter_var($input['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Website must be a valid URL.';
        }
    }

    /**
     * Validate freelancer only fields.
     */
    private function validateFreelancerFields(array $input, array &$errors): void
    {
        if ($input['dateOfBirth'] === '') {
            $errors['dateOfBirth'] = 'Date of birth is required for freelancers.';
            return;
        }

        if (!$this->isValidDate($input['dateOfBirth'])) {
            $errors['dateOfBirth'] = 'Please enter a valid date of birth.';
        }
    }

    /**
     * Run the user creation flow inside a transaction.
     */
    private function registerInTransaction(array $normalizedInput): array
    {
        $this->pdo->beginTransaction();

        try {
            $userId = $this->createUser($normalizedInput);
            $this->createRoleRecord($userId, $normalizedInput);
            $this->pdo->commit();

            return $this->buildSuccessResult($userId, $normalizedInput['role']);
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return $this->buildFailureResult(
                ['general' => 'Unable to create your account right now. Please try again.'],
                $normalizedInput
            );
        }
    }

    /**
     * Create the base user record.
     */
    private function createUser(array $normalizedInput): int
    {
        return $this->userRepository->create([
            'username' => $normalizedInput['username'],
            'name' => $normalizedInput['name'],
            'email' => $normalizedInput['email'],
            'password' => password_hash($normalizedInput['password'], PASSWORD_DEFAULT),
            'role' => $normalizedInput['role'],
            'address' => $normalizedInput['address'],
            'bio' => $normalizedInput['bio'],
            'kvkNr' => $normalizedInput['kvkNr'],
        ]);
    }

    /**
     * Create the role-specific record for the new user.
     */
    private function createRoleRecord(int $userId, array $normalizedInput): void
    {
        if ($normalizedInput['role'] === RoleType::PRODUCTION_HOUSE->value) {
            $this->productionHouseRepository->create($userId, [
                'companyName' => $normalizedInput['companyName'],
                'website' => $normalizedInput['website'],
            ]);

            return;
        }

        if ($normalizedInput['role'] === RoleType::FREELANCER->value) {
            $this->freelancerRepository->create($userId, [
                'dateOfBirth' => $normalizedInput['dateOfBirth'],
            ]);
        }
    }

    /**
     * Return a standardized success payload.
     */
    private function buildSuccessResult(int $userId, string $role): array
    {
        return [
            'success' => true,
            'userId' => $userId,
            'role' => $role,
        ];
    }

    /**
     * Validate the provided date string.
     */
    private function isValidDate(string $date): bool
    {
        $dateTime = date_create_from_format('Y-m-d', $date);

        return $dateTime !== false && $dateTime->format('Y-m-d') === $date;
    }
}

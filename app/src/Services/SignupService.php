<?php

namespace App\Services;

use App\Repositories\FreelancerRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
use PDO;
use Throwable;

class SignupService
{
    private UserRepository $userRepository;
    private ProductionHouseRepository $productionHouseRepository;
    private FreelancerRepository $freelancerRepository;

    public function __construct(private readonly PDO $pdo)
    {
        $this->userRepository = new UserRepository($pdo);
        $this->productionHouseRepository = new ProductionHouseRepository($pdo);
        $this->freelancerRepository = new FreelancerRepository($pdo);
    }

    public function register(array $input): array
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
            $this->pdo->beginTransaction();

            $userId = $this->userRepository->create([
                'username' => $normalizedInput['username'],
                'name' => $normalizedInput['name'],
                'email' => $normalizedInput['email'],
                'password' => password_hash($normalizedInput['password'], PASSWORD_DEFAULT),
                'role' => $normalizedInput['role'],
                'address' => $normalizedInput['address'],
                'bio' => $normalizedInput['bio'],
                'kvkNr' => $normalizedInput['kvkNr'],
            ]);

            if ($normalizedInput['role'] === 'productionHouse') {
                $this->productionHouseRepository->create($userId, [
                    'companyName' => $normalizedInput['companyName'],
                    'website' => $normalizedInput['website'],
                ]);
            }

            if ($normalizedInput['role'] === 'freelance') {
                $this->freelancerRepository->create($userId, [
                    'dateOfBirth' => $normalizedInput['dateOfBirth'],
                ]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'userId' => $userId,
                'role' => $normalizedInput['role'],
            ];
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'success' => false,
                'errors' => [
                    'general' => 'Unable to create your account right now. Please try again.',
                ],
                'input' => $normalizedInput,
            ];
        }
    }

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

    private function validate(array $input): array
    {
        $errors = [];

        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
        }

        if ($input['name'] === '') {
            $errors['name'] = 'Full name is required.';
        }

        if ($input['email'] === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif ($this->userRepository->emailExists($input['email'])) {
            $errors['email'] = 'An account with this email already exists.';
        }

        if ($input['password'] === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($input['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }

        if (!in_array($input['role'], ['productionHouse', 'freelance'], true)) {
            $errors['role'] = 'Please choose a valid account type.';
        }

        if ($input['kvkNr'] === '') {
            $errors['kvkNr'] = 'Chamber of Commerce number is required.';
        } elseif (!preg_match('/^\d{8}$/', $input['kvkNr'])) {
            $errors['kvkNr'] = 'Chamber of Commerce number must contain exactly 8 digits.';
        } elseif ($this->userRepository->kvkNrExists($input['kvkNr'])) {
            $errors['kvkNr'] = 'This Chamber of Commerce number is already registered.';
        }

        if ($input['role'] === 'productionHouse') {
            if ($input['companyName'] === '') {
                $errors['companyName'] = 'Company name is required for production companies.';
            }

            if ($input['website'] !== '' && !filter_var($input['website'], FILTER_VALIDATE_URL)) {
                $errors['website'] = 'Website must be a valid URL.';
            }
        }

        if ($input['role'] === 'freelance') {
            if ($input['dateOfBirth'] === '') {
                $errors['dateOfBirth'] = 'Date of birth is required for freelancers.';
            } elseif (!$this->isValidDate($input['dateOfBirth'])) {
                $errors['dateOfBirth'] = 'Please enter a valid date of birth.';
            }
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $dateTime = date_create_from_format('Y-m-d', $date);

        return $dateTime !== false && $dateTime->format('Y-m-d') === $date;
    }
}

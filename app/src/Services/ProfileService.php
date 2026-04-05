<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Framework\Service;
use App\Models\Freelancer;
use App\Models\ProductionHouse;
use App\Models\User;
use App\Repositories\FreelancerRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IProfileService;
use DateTime;
use PDO;
use Throwable;

class ProfileService extends Service implements IProfileService
{
    private const PROFILE_UPLOAD_DIRECTORY = __DIR__ . '/../../public/assets/images';
    private const PROFILE_UPLOAD_PREFIX = 'profile-user-';
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const DEFAULT_ADMIN_PROFILE_IMAGE = 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?auto=format&fit=crop&w=220&q=80';
    private const DEFAULT_FREELANCER_PROFILE_IMAGE = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=220&q=80';

    private UserRepository $userRepository;
    private ProductionHouseRepository $productionHouseRepository;
    private FreelancerRepository $freelancerRepository;

    /**
     * Build the profile service with repository dependencies.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userRepository = new UserRepository($pdo);
        $this->productionHouseRepository = new ProductionHouseRepository($pdo);
        $this->freelancerRepository = new FreelancerRepository($pdo);
    }

    /**
     * Return editable profile data for admin and production users.
     */
    public function getAdminProfileData(int $userId): array
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            return $this->defaultAdminData();
        }

        $productionHouse = $this->productionHouseRepository->findByUserId($userId);

        return [
            'profileImage' => $this->resolveProfileImageUrl($user),
            'form' => [
                'username' => $user->getUsername(),
                'companyName' => $productionHouse?->getCompanyName() ?? $user->getName(),
                'contactName' => $user->getName(),
                'email' => $user->getEmail(),
                'address' => $user->getAddress(),
                'website' => $productionHouse?->getWebsite() ?? '',
                'bio' => $user->getBio(),
            ],
        ];
    }

    /**
     * Return editable profile data for freelancer users.
     */
    public function getFreelancerProfileData(int $userId): array
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            return $this->defaultFreelancerData();
        }

        $freelancer = $this->freelancerRepository->findByUserId($userId);

        return [
            'profileImage' => $this->resolveProfileImageUrl($user),
            'form' => [
                'username' => $user->getUsername(),
                'fullName' => $user->getName(),
                'email' => $user->getEmail(),
                'address' => $user->getAddress(),
                'dateOfBirth' => $freelancer?->getDateOfBirth() ?? '',
                'bio' => $user->getBio(),
            ],
        ];
    }

    /**
     * Return read-only public profile data for production users.
     */
    public function getProductionHousePublicProfileData(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null || !in_array((string) $user->getRole(), [RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value], true)) {
            return null;
        }

        $productionHouse = $this->productionHouseRepository->findByUserId($userId);

        return [
            'pageTitle' => trim(($productionHouse?->getCompanyName() ?: $user->getName()) . ' - Production Profile - FilmGig'),
            'profileImage' => $this->resolvePublicProfileImageUrlByUserId($userId),
            'contactName' => $user->getName(),
            'companyName' => $productionHouse?->getCompanyName() ?: $user->getName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'website' => $productionHouse?->getWebsite() ?? '',
            'bio' => $user->getBio(),
        ];
    }

    /**
     * Return read-only public profile data for freelancer users.
     */
    public function getFreelancerPublicProfileData(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        $user = $this->userRepository->findById($userId);

        if ($user === null || (string) $user->getRole() !== RoleType::FREELANCER->value) {
            return null;
        }

        $freelancer = $this->freelancerRepository->findByUserId($userId);

        return [
            'pageTitle' => trim($user->getName() . ' - Freelancer Profile - FilmGig'),
            'profileImage' => $this->resolvePublicProfileImageUrlByUserId($userId, RoleType::FREELANCER->value),
            'fullName' => $user->getName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'dateOfBirth' => $freelancer?->getDateOfBirth() ?? '',
            'bio' => $user->getBio(),
        ];
    }

    /**
     * Update profile data for admin and production users.
     */
    public function updateAdminProfile(int $userId, array $input, array $profileImageFile): array
    {
        $resolvedUser = $this->resolveUserForUpdate($userId);

        if (($resolvedUser['failure'] ?? null) !== null) {
            return $resolvedUser['failure'];
        }

        /** @var User $user */
        $user = $resolvedUser['user'];
        $normalizedInput = $this->normalizeAdminInput($input);
        $errors = $this->validateAdminProfileUpdate($user, $normalizedInput, $profileImageFile);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'input' => $normalizedInput,
            ];
        }

        if (!$this->persistAdminProfileUpdate($user, $normalizedInput)) {
            return $this->buildGeneralFailure('Unable to update your profile right now. Please try again.');
        }

        return $this->completeProfileUpdate($userId, $profileImageFile);
    }

    /**
     * Update profile data for freelancer users.
     */
    public function updateFreelancerProfile(int $userId, array $input, array $profileImageFile): array
    {
        $resolvedUser = $this->resolveUserForUpdate($userId);

        if (($resolvedUser['failure'] ?? null) !== null) {
            return $resolvedUser['failure'];
        }

        /** @var User $user */
        $user = $resolvedUser['user'];
        $normalizedInput = $this->normalizeFreelancerInput($input);
        $errors = $this->validateFreelancerProfileUpdate($user, $normalizedInput, $profileImageFile);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'input' => $normalizedInput,
            ];
        }

        if (!$this->persistFreelancerProfileUpdate($user, $normalizedInput)) {
            return $this->buildGeneralFailure('Unable to update your profile right now. Please try again.');
        }

        return $this->completeProfileUpdate($userId, $profileImageFile);
    }

    /**
     * Resolve the user record for update flows.
     */
    private function resolveUserForUpdate(int $userId): array
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            return ['user' => null, 'failure' => $this->buildGeneralFailure('Unable to update your profile right now.')];
        }

        return ['user' => $user, 'failure' => null];
    }

    /**
     * Validate admin/production house profile update input and image.
     */
    private function validateAdminProfileUpdate(User $user, array $normalizedInput, array $profileImageFile): array
    {
        $errors = $this->validateAdminInput($user, $normalizedInput);

        return array_merge($errors, $this->validateProfileImage($profileImageFile));
    }

    /**
     * Validate freelancer profile update input and image.
     */
    private function validateFreelancerProfileUpdate(User $user, array $normalizedInput, array $profileImageFile): array
    {
        $errors = $this->validateFreelancerInput($user, $normalizedInput);

        return array_merge($errors, $this->validateProfileImage($profileImageFile));
    }

    /**
     * Persist admin/production house profile data inside a transaction.
     */
    private function persistAdminProfileUpdate(User $user, array $normalizedInput): bool
    {
        $productionHouse = $this->productionHouseRepository->findByUserId($user->getUserId());

        try {
            $this->pdo->beginTransaction();

            if (!$this->updateUserRecord($user, $normalizedInput)) {
                $this->pdo->rollBack();
                return false;
            }

            if ($user->getRole() === 'productionHouse') {
                if (!$this->upsertProductionHouseRecord($productionHouse, $user->getUserId(), $normalizedInput)) {
                    $this->pdo->rollBack();
                    return false;
                }
            }

            $this->pdo->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    /**
     * Persist freelancer profile data inside a transaction.
     */
    private function persistFreelancerProfileUpdate(User $user, array $normalizedInput): bool
    {
        $freelancer = $this->freelancerRepository->findByUserId($user->getUserId());

        try {
            $this->pdo->beginTransaction();

            if (!$this->updateUserRecord($user, $normalizedInput)) {
                $this->pdo->rollBack();
                return false;
            }

            if (!$this->upsertFreelancerRecord($freelancer, $user->getUserId(), (string) $normalizedInput['dateOfBirth'])) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    /**
     * Complete profile update by handling optional profile image upload and success payload.
     */
    private function completeProfileUpdate(int $userId, array $profileImageFile): array
    {
        if (!$this->storeProfileImage($userId, $profileImageFile)) {
            return $this->buildGeneralFailure('Profile details saved, but we could not upload your profile picture.');
        }

        return [
            'success' => true,
            'message' => 'Your profile has been updated successfully.',
        ];
    }

    /**
     * Build default admin profile data.
     */
    private function defaultAdminData(): array
    {
        return [
            'profileImage' => self::DEFAULT_ADMIN_PROFILE_IMAGE,
            'form' => [
                'username' => '',
                'companyName' => '',
                'contactName' => '',
                'email' => '',
                'address' => '',
                'website' => '',
                'bio' => '',
            ],
        ];
    }

    /**
     * Build default freelancer profile data.
     */
    private function defaultFreelancerData(): array
    {
        return [
            'profileImage' => self::DEFAULT_FREELANCER_PROFILE_IMAGE,
            'form' => [
                'username' => '',
                'fullName' => '',
                'email' => '',
                'address' => '',
                'dateOfBirth' => '',
                'bio' => '',
            ],
        ];
    }

    /**
     * Resolve public profile image URL by user id and role.
     */
    private function resolvePublicProfileImageUrlByUserId(int $userId, string $role = RoleType::PRODUCTION_HOUSE->value): string
    {
        $pattern = __DIR__ . '/../../public/assets/images/profile-user-' . $userId . '.*';
        $matches = glob($pattern) ?: [];

        if (!empty($matches)) {
            return '/assets/images/' . basename($matches[0]);
        }

        if ($role === RoleType::FREELANCER->value) {
            return self::DEFAULT_FREELANCER_PROFILE_IMAGE;
        }

        return self::DEFAULT_ADMIN_PROFILE_IMAGE;
    }

    /**
     * Normalize editable input for admin and production users.
     */
    private function normalizeAdminInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'companyName' => trim((string) ($input['companyName'] ?? '')),
            'contactName' => trim((string) ($input['contactName'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'address' => trim((string) ($input['address'] ?? '')),
            'website' => trim((string) ($input['website'] ?? '')),
            'bio' => trim((string) ($input['bio'] ?? '')),
        ];
    }

    /**
     * Normalize editable input for freelancer users.
     */
    private function normalizeFreelancerInput(array $input): array
    {
        return [
            'username' => trim((string) ($input['username'] ?? '')),
            'fullName' => trim((string) ($input['fullName'] ?? '')),
            'email' => trim((string) ($input['email'] ?? '')),
            'address' => trim((string) ($input['address'] ?? '')),
            'dateOfBirth' => trim((string) ($input['dateOfBirth'] ?? '')),
            'bio' => trim((string) ($input['bio'] ?? '')),
        ];
    }

    /**
     * Validate admin and production profile fields.
     */
    private function validateAdminInput(User $currentUser, array $input): array
    {
        $errors = [];

        $this->validateUsernameField($input, $errors);
        $this->validateAdminIdentityFields($input, $errors);
        $this->validateEmailField($input, $errors);
        $this->validateAddressField($input, $errors);
        $this->validateWebsiteField($input, $errors);

        return array_merge($errors, $this->validateUserUniqueness($currentUser, $input['username'], $input['email']));
    }

    /**
     * Validate freelancer profile fields.
     */
    private function validateFreelancerInput(User $currentUser, array $input): array
    {
        $errors = [];

        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif (preg_match('/^[A-Za-z0-9.]+$/', $input['username']) !== 1) {
            $errors['username'] = 'Username may only contain letters, numbers, and dots (.).';
        }

        if ($input['fullName'] === '') {
            $errors['fullName'] = 'Full name is required.';
        }

        if (!$this->isValidEmail($input['email'])) {
            $errors['email'] = 'Please provide a valid email address.';
        }

        if ($input['address'] === '') {
            $errors['address'] = 'Address is required.';
        }

        if ($input['dateOfBirth'] !== '' && !$this->isValidDate($input['dateOfBirth'])) {
            $errors['dateOfBirth'] = 'Date of birth must be a valid date.';
        }

        return array_merge($errors, $this->validateUserUniqueness($currentUser, $input['username'], $input['email']));
    }

    /**
     * Validate username and email uniqueness for one user.
     */
    private function validateUserUniqueness(User $currentUser, string $username, string $email): array
    {
        $errors = [];

        $existingByUsername = $this->userRepository->findByUsername($username);
        if ($existingByUsername !== null && $existingByUsername->getUserId() !== $currentUser->getUserId()) {
            $errors['username'] = 'This username is already taken.';
        }

        $existingByEmail = $this->userRepository->findByEmail($email);
        if ($existingByEmail !== null && $existingByEmail->getUserId() !== $currentUser->getUserId()) {
            $errors['email'] = 'This email address is already in use.';
        }

        return $errors;
    }

    /**
     * Validate a profile image upload.
     */
    private function validateProfileImage(array $file): array
    {
        $metadataError = $this->validateProfileImageMetadata($file);

        if ($metadataError !== null) {
            return $metadataError;
        }

        return $this->validateProfileImageContent((string) ($file['tmp_name'] ?? ''));
    }

    /**
     * Store a profile image for a user.
     */
    private function storeProfileImage(int $userId, array $file): bool
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return true;
        }

        if (!is_dir(self::PROFILE_UPLOAD_DIRECTORY) && !mkdir(self::PROFILE_UPLOAD_DIRECTORY, 0775, true) && !is_dir(self::PROFILE_UPLOAD_DIRECTORY)) {
            return false;
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $imageInfo = @getimagesize($tmpPath);
        $mimeType = (string) ($imageInfo['mime'] ?? '');
        $extension = $this->imageMimeTypeToExtension($mimeType);

        if ($extension === null) {
            return false;
        }

        $this->deleteExistingProfileImages($userId);

        $fileName = self::PROFILE_UPLOAD_PREFIX . $userId . '.' . $extension;
        $targetPath = self::PROFILE_UPLOAD_DIRECTORY . '/' . $fileName;

        return move_uploaded_file($tmpPath, $targetPath);
    }

    /**
     * Delete any existing profile images for one user.
     */
    private function deleteExistingProfileImages(int $userId): void
    {
        $pattern = self::PROFILE_UPLOAD_DIRECTORY . '/' . self::PROFILE_UPLOAD_PREFIX . $userId . '.*';
        $existingFiles = glob($pattern) ?: [];

        foreach ($existingFiles as $existingFile) {
            if (is_file($existingFile)) {
                @unlink($existingFile);
            }
        }
    }

    /**
     * Resolve the profile image URL for editable profile pages.
     */
    private function resolveProfileImageUrl(User $user): string
    {
        $pattern = self::PROFILE_UPLOAD_DIRECTORY . '/' . self::PROFILE_UPLOAD_PREFIX . $user->getUserId() . '.*';
        $matches = glob($pattern) ?: [];

        if (!empty($matches)) {
            return '/assets/images/' . basename($matches[0]);
        }

        return $user->getRole() === 'freelancer'
            ? self::DEFAULT_FREELANCER_PROFILE_IMAGE
            : self::DEFAULT_ADMIN_PROFILE_IMAGE;
    }

    /**
     * Update the base user record from normalized profile input.
     */
    private function updateUserRecord(User $currentUser, array $input): bool
    {
        $name = (string) ($input['contactName'] ?? $input['fullName'] ?? $currentUser->getName());

        return $this->userRepository->update($currentUser->getUserId(), [
            'username' => (string) ($input['username'] ?? $currentUser->getUsername()),
            'name' => $name,
            'email' => (string) ($input['email'] ?? $currentUser->getEmail()),
            'password' => $currentUser->getPassword(),
            'role' => $currentUser->getRole(),
            'address' => (string) ($input['address'] ?? $currentUser->getAddress()),
            'bio' => (string) ($input['bio'] ?? $currentUser->getBio()),
            'kvkNr' => $currentUser->getKvkNr(),
        ]);
    }

    /**
     * Upsert a production house profile record.
     */
    private function upsertProductionHouseRecord(?ProductionHouse $productionHouse, int $userId, array $input): bool
    {
        $payload = [
            'userId' => $userId,
            'companyName' => (string) ($input['companyName'] ?? ''),
            'website' => (string) ($input['website'] ?? ''),
        ];

        if ($productionHouse === null) {
            return $this->productionHouseRepository->create($userId, $payload) > 0;
        }

        return $this->productionHouseRepository->update($productionHouse->getProductionHouseId(), $payload);
    }

    /**
     * Upsert a freelancer profile record.
     */
    private function upsertFreelancerRecord(?Freelancer $freelancer, int $userId, string $dateOfBirth): bool
    {
        $payload = [
            'userId' => $userId,
            'dateOfBirth' => $dateOfBirth !== '' ? $dateOfBirth : null,
        ];

        if ($freelancer === null) {
            return $this->freelancerRepository->create($userId, $payload) > 0;
        }

        return $this->freelancerRepository->update($freelancer->getFreelancerId(), $payload);
    }

    /**
     * Check whether an email string is valid.
     */
    private function isValidEmail(string $email): bool
    {
        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check whether a date string matches YYYY-MM-DD.
     */
    private function isValidDate(string $value): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    /**
     * Validate the shared username field.
     */
    private function validateUsernameField(array $input, array &$errors): void
    {
        if ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
            return;
        }

        if (preg_match('/^[A-Za-z0-9.]+$/', $input['username']) !== 1) {
            $errors['username'] = 'Username may only contain letters, numbers, and dots (.).';
        }
    }

    /**
     * Validate shared contact fields for admin and production users.
     */
    private function validateAdminIdentityFields(array $input, array &$errors): void
    {
        if ($input['companyName'] === '') {
            $errors['companyName'] = 'Company name is required.';
        }

        if ($input['contactName'] === '') {
            $errors['contactName'] = 'Contact person name is required.';
        }
    }

    /**
     * Validate the shared email field.
     */
    private function validateEmailField(array $input, array &$errors): void
    {
        if (!$this->isValidEmail($input['email'])) {
            $errors['email'] = 'Please provide a valid email address.';
        }
    }

    /**
     * Validate the shared address field.
     */
    private function validateAddressField(array $input, array &$errors): void
    {
        if ($input['address'] === '') {
            $errors['address'] = 'Address is required.';
        }
    }

    /**
     * Validate the optional website field for admin and production users.
     */
    private function validateWebsiteField(array $input, array &$errors): void
    {
        if ($input['website'] !== '' && filter_var($input['website'], FILTER_VALIDATE_URL) === false) {
            $errors['website'] = 'Please provide a valid website URL.';
        }
    }

    /**
     * Validate upload metadata for a profile image.
     */
    private function validateProfileImageMetadata(array $file): ?array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return [];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_FORM_SIZE) {
            return ['profileImage' => 'Profile image must be smaller than 5 MB.'];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['profileImage' => 'The profile image could not be uploaded.'];
        }

        if ((int) ($file['size'] ?? 0) > self::MAX_IMAGE_SIZE) {
            return ['profileImage' => 'Profile image must be smaller than 5 MB.'];
        }

        return null;
    }

    /**
     * Validate image content and MIME type for a profile image.
     */
    private function validateProfileImageContent(string $tmpPath): array
    {
        $imageInfo = @getimagesize($tmpPath);

        if ($imageInfo === false) {
            return ['profileImage' => 'Profile image must be a valid image file.'];
        }

        $mimeType = (string) ($imageInfo['mime'] ?? '');

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            return ['profileImage' => 'Profile image must be JPG, PNG, GIF, or WEBP.'];
        }

        return [];
    }
}

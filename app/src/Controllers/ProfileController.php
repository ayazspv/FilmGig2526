<?php 

namespace App\Controllers;

use App\Config;
use App\Enums\RoleType;
use App\Framework\Controller;
use App\Repositories\FreelancerRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
use App\Services\Interfaces\IProfileService;
use App\Services\ProfileService;
use App\ViewModels\AdminProfileViewModel;
use App\ViewModels\FreelancerProfileViewModel;

class ProfileController extends Controller
{
    /**
     * Route authenticated users to the correct profile page by role.
     */
    public function showProfile(array $params = []): void
    {
        $this->requireAuthentication();

        $role = $this->authUserRole();

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminProfile();
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerProfile();
            return;
        }

        $this->redirect('/signin');
    }

    /**
     * Render the admin or production house profile page.
     */
    public function showAdminProfile(): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $profileData = $this->getProfileService()->getAdminProfileData($this->authenticatedUserId());
        $oldInput = $this->consumeArrayFlashMessage('profile_old_input');
        $errors = $this->consumeArrayFlashMessage('profile_errors');
        $successMessage = $this->consumeStringFlashMessage('profile_success_message');

        if (!empty($oldInput)) {
            $profileData['form'] = array_merge($profileData['form'] ?? [], $oldInput);
        }

        $viewModel = AdminProfileViewModel::createFromData($profileData, $errors, $successMessage);

        include __DIR__ . '/../Views/profiles/adminProfile.php';
    }

    /**
     * Render the freelancer profile page.
     */
    public function showFreelancerProfile(): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $profileData = $this->getProfileService()->getFreelancerProfileData($this->authenticatedUserId());
        $oldInput = $this->consumeArrayFlashMessage('profile_old_input');
        $errors = $this->consumeArrayFlashMessage('profile_errors');
        $successMessage = $this->consumeStringFlashMessage('profile_success_message');

        if (!empty($oldInput)) {
            $profileData['form'] = array_merge($profileData['form'] ?? [], $oldInput);
        }

        $viewModel = FreelancerProfileViewModel::createFromData($profileData, $errors, $successMessage);

        include __DIR__ . '/../Views/profiles/freelancerProfile.php';
    }

    /**
     * Render a read-only public profile page for a production house/admin.
     */
    public function showProductionHousePublicProfile(array $params = []): void
    {
        $userId = (int) ($params['id'] ?? 0);

        if ($userId <= 0) {
            $this->redirect('/gigs');
        }

        $userRepository = new UserRepository(Config::pdo());
        $productionHouseRepository = new ProductionHouseRepository(Config::pdo());

        $user = $userRepository->findById($userId);

        if ($user === null || !in_array((string) $user->getRole(), [RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value], true)) {
            $this->redirect('/gigs');
        }

        $productionHouse = $productionHouseRepository->findByUserId($userId);

        $viewData = [
            'pageTitle' => trim(($productionHouse?->getCompanyName() ?: $user->getName()) . ' - Production Profile - FilmGig'),
            'profileImage' => $this->resolvePublicProfileImageUrl($userId),
            'contactName' => $user->getName(),
            'companyName' => $productionHouse?->getCompanyName() ?: $user->getName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'website' => $productionHouse?->getWebsite() ?? '',
            'bio' => $user->getBio(),
        ];

        include __DIR__ . '/../Views/profiles/productionHousePublicProfile.php';
    }

    /**
     * Render a read-only public profile page for a freelancer.
     */
    public function showFreelancerPublicProfile(array $params = []): void
    {
        $userId = (int) ($params['id'] ?? 0);

        if ($userId <= 0) {
            $this->redirect('/gigs');
        }

        $userRepository = new UserRepository(Config::pdo());
        $freelancerRepository = new FreelancerRepository(Config::pdo());

        $user = $userRepository->findById($userId);

        if ($user === null || (string) $user->getRole() !== RoleType::FREELANCER->value) {
            $this->redirect('/gigs');
        }

        $freelancer = $freelancerRepository->findByUserId($userId);

        $viewData = [
            'pageTitle' => trim($user->getName() . ' - Freelancer Profile - FilmGig'),
            'profileImage' => $this->resolvePublicProfileImageUrl($userId, RoleType::FREELANCER->value),
            'fullName' => $user->getName(),
            'email' => $user->getEmail(),
            'address' => $user->getAddress(),
            'dateOfBirth' => $freelancer?->getDateOfBirth() ?? '',
            'bio' => $user->getBio(),
        ];

        include __DIR__ . '/../Views/profiles/freelancerPublicProfile.php';
    }

    /**
     * Handle profile updates for the authenticated user by role.
     */
    public function handleProfileUpdate(array $params = []): void
    {
        $this->requireAuthentication();

        $role = $this->authUserRole();

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->handleAdminProfileUpdate();
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->handleFreelancerProfileUpdate();
            return;
        }

        $this->redirect('/signin');
    }

    /**
     * Persist admin/production house profile updates.
     */
    private function handleAdminProfileUpdate(): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $result = $this->getProfileService()->updateAdminProfile(
            $this->authenticatedUserId(),
            $_POST,
            $_FILES['profileImage'] ?? []
        );

        $this->handleProfileUpdateResult($result, (string) ($_POST['contactName'] ?? ''));
    }

    /**
     * Persist freelancer profile updates.
     */
    private function handleFreelancerProfileUpdate(): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $result = $this->getProfileService()->updateFreelancerProfile(
            $this->authenticatedUserId(),
            $_POST,
            $_FILES['profileImage'] ?? []
        );

        $this->handleProfileUpdateResult($result, (string) ($_POST['fullName'] ?? ''));
    }

    /**
     * Persist flash data and redirect after profile update attempts.
     */
    private function handleProfileUpdateResult(array $result, string $updatedName): void
    {
        if (($result['success'] ?? false) === true) {
            $_SESSION['profile_success_message'] = (string) ($result['message'] ?? 'Your profile has been updated successfully.');

            if ($updatedName !== '') {
                $_SESSION['auth_user_name'] = $updatedName;
            }

            $this->redirect('/profile');
            return;
        }

        $_SESSION['profile_errors'] = $result['errors'] ?? ['general' => 'Unable to update your profile right now.'];
        $_SESSION['profile_old_input'] = $result['input'] ?? [];

        $this->redirect('/profile');
    }

    /**
     * Return the authenticated session user id.
     */
    private function authenticatedUserId(): int
    {
        return (int) ($_SESSION['auth_user_id'] ?? 0);
    }

    /**
     * Build profile service.
     */
    private function getProfileService(): IProfileService
    {
        return new ProfileService(Config::pdo());
    }

    /**
     * Consume one-time string message.
     */
    private function consumeStringFlashMessage(string $key): ?string
    {
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);

        return $message !== null ? (string) $message : null;
    }

    /**
     * Consume one-time array message.
     */
    private function consumeArrayFlashMessage(string $key): array
    {
        $value = $_SESSION[$key] ?? [];
        unset($_SESSION[$key]);

        return is_array($value) ? $value : [];
    }

    /**
     * Resolve a profile image URL for public profile pages.
     */
    private function resolvePublicProfileImageUrl(int $userId, string $role = RoleType::PRODUCTION_HOUSE->value): string
    {
        $pattern = __DIR__ . '/../../public/assets/images/profile-user-' . $userId . '.*';
        $matches = glob($pattern) ?: [];

        if (!empty($matches)) {
            return '/assets/images/' . basename($matches[0]);
        }

        if ($role === RoleType::FREELANCER->value) {
            return 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=220&q=80';
        }

        return 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?auto=format&fit=crop&w=220&q=80';
    }

}
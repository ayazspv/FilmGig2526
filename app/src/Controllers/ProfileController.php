<?php 

namespace App\Controllers;

use App\Enums\RoleType;
use App\Framework\Controller;
use App\Services\Interfaces\IProfileService;
use App\Config;
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
        $userId = $this->extractPublicProfileUserId($params);
        $viewData = $this->getProfileService()->getProductionHousePublicProfileData($userId);

        if ($viewData === null) {
            $this->redirect('/gigs');
        }

        include __DIR__ . '/../Views/profiles/productionHousePublicProfile.php';
    }

    /**
     * Render a read-only public profile page for a freelancer.
     */
    public function showFreelancerPublicProfile(array $params = []): void
    {
        $userId = $this->extractPublicProfileUserId($params);
        $viewData = $this->getProfileService()->getFreelancerPublicProfileData($userId);

        if ($viewData === null) {
            $this->redirect('/gigs');
        }

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
     * Build the profile service instance.
     */
    private function getProfileService(): IProfileService
    {
        return new ProfileService(Config::pdo());
    }

    /**
     * Extract and validate a public profile user id from route params.
     */
    private function extractPublicProfileUserId(array $params): int
    {
        $userId = (int) ($params['id'] ?? 0);

        if ($userId <= 0) {
            $this->redirect('/gigs');
        }

        return $userId;
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

}
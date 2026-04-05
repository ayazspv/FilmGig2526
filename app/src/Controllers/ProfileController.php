<?php 

namespace App\Controllers;

use App\Enums\RoleType;
use App\ViewModels\AdminProfileViewModel;
use App\ViewModels\FreelancerProfileViewModel;

class ProfileController
{
    public function showProfile(array $params = []): void
    {
        if (!$this->ensureAuthenticated()) {
            return;
        }

        $role = (string) ($_SESSION['auth_user_role'] ?? '');

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminProfile();
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerProfile();
            return;
        }

        header('Location: /signin');
        exit;
    }

    public function showAdminProfile(): void
    {
        if (!$this->ensureRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value])) {
            return;
        }

        $viewModel = AdminProfileViewModel::createDefault();

        include __DIR__ . '/../Views/profiles/adminProfile.php';
    }

    public function showFreelancerProfile(): void
    {
        if (!$this->ensureRole([RoleType::FREELANCER->value])) {
            return;
        }

        $viewModel = FreelancerProfileViewModel::createDefault();

        include __DIR__ . '/../Views/profiles/freelancerProfile.php';
    }

    private function ensureAuthenticated(): bool
    {
        if (!empty($_SESSION['auth_user_id']) && !empty($_SESSION['auth_user_role'])) {
            return true;
        }

        header('Location: /signin');
        exit;
    }

    private function ensureRole(array $allowedRoles): bool
    {
        if (!$this->ensureAuthenticated()) {
            return false;
        }

        $currentRole = (string) ($_SESSION['auth_user_role'] ?? '');

        if (in_array($currentRole, $allowedRoles, true)) {
            return true;
        }

        header('Location: /dashboard');
        exit;
    }
}
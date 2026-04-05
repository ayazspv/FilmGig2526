<?php

namespace App\Controllers;

use App\Enums\RoleType;
use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;
use App\ViewModels\AdminGigPostingViewModel;
use App\ViewModels\AdminGigEditingViewModel;
use App\ViewModels\AdminGigListingViewModel;

class DashboardController
{
    public function showDashboard(array $params = []): void
    {
        if (!$this->ensureAuthenticated()) {
            return;
        }

        $role = (string) ($_SESSION['auth_user_role'] ?? '');

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminDashboard($params);
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerDashboard($params);
            return;
        }

        $this->denyAndRedirectToSignin();
    }

    public function showAdminDashboard(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value])) {
            return;
        }

        $viewModel = AdminDashboardViewModel::createAdminDefault();

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    public function showFreelancerDashboard(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::FREELANCER->value])) {
            return;
        }

        $viewModel = FreelancerDashboardViewModel::createFreelancerDefault();

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
    }

    public function showAdminGigListing(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value])) {
            return;
        }

        $viewModel = AdminGigListingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigListing.php';
    }

    public function showAdminGigPosting(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value])) {
            return;
        }

        $viewModel = AdminGigPostingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    public function showAdminGigEditing(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value])) {
            return;
        }

        $viewModel = AdminGigEditingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
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

    private function denyAndRedirectToSignin(): void
    {
        session_unset();
        session_destroy();

        header('Location: /signin');
        exit;
    }

}
<?php

namespace App\Controllers;

use App\Enums\RoleType;
use App\ViewModels\AdminSettingsViewModel;
use App\ViewModels\FreelancerSettingsViewModel;

class SettingsController
{
    public function showSettings(array $params = []): void
    {
        if (!$this->ensureAuthenticated()) {
            return;
        }

        $role = (string) ($_SESSION['auth_user_role'] ?? '');

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminSettings($params);
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerSettings($params);
            return;
        }

        header('Location: /signin');
        exit;
    }

    public function showAdminSettings(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value])) {
            return;
        }

        $viewModel = AdminSettingsViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/settings.php';
    }

    public function showFreelancerSettings(array $params = []): void
    {
        if (!$this->ensureRole([RoleType::FREELANCER->value])) {
            return;
        }

        $viewModel = FreelancerSettingsViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/freelancer/settings.php';
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
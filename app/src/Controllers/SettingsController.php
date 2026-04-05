<?php

namespace App\Controllers;

use App\Enums\RoleType;
use App\Framework\Controller;
use App\ViewModels\AdminSettingsViewModel;
use App\ViewModels\FreelancerSettingsViewModel;

class SettingsController extends Controller
{
    /**
     * Route authenticated users to the correct settings page by role.
     */
    public function showSettings(array $params = []): void
    {
        $this->requireAuthentication();

        $role = $this->authUserRole();

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminSettings($params);
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerSettings($params);
            return;
        }

        $this->redirect('/signin');
    }

    /**
     * Render the admin or production house settings page.
     */
    public function showAdminSettings(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminSettingsViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/settings.php';
    }

    /**
     * Render the freelancer settings page.
     */
    public function showFreelancerSettings(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $viewModel = FreelancerSettingsViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/freelancer/settings.php';
    }

}
<?php

namespace App\Controllers;

use App\ViewModels\AdminSettingsViewModel;
use App\ViewModels\FreelancerSettingsViewModel;

class SettingsController
{
    public function showAdminSettings(array $params = []): void
    {
        $viewModel = AdminSettingsViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/settings.php';
    }

    public function showFreelancerSettings(array $params = []): void
    {
        $viewModel = FreelancerSettingsViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/freelancer/settings.php';
    }
}
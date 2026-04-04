<?php

namespace App\Controllers;

use App\ViewModels\DashboardViewModel;

class DashboardController
{
    public function showAdminDashboard(array $params = []): void
    {
        $viewModel = DashboardViewModel::createAdminDefault();

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    public function showFreelancerDashboard(array $params = []): void
    {
        $viewModel = DashboardViewModel::createFreelancerDefault();

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
    }

}
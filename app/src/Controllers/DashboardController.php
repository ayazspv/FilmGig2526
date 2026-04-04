<?php

namespace App\Controllers;

use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;

class DashboardController
{
    public function showAdminDashboard(array $params = []): void
    {
        $viewModel = AdminDashboardViewModel::createAdminDefault();

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    public function showFreelancerDashboard(array $params = []): void
    {
        $viewModel = FreelancerDashboardViewModel::createFreelancerDefault();

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
    }

}
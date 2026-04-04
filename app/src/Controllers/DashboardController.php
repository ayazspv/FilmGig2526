<?php

namespace App\Controllers;

use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;
use App\ViewModels\AdminGigPostingViewModel;
use App\ViewModels\AdminGigEditingViewModel;
use App\ViewModels\AdminGigListingViewModel;

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

    public function showAdminGigListing(array $params = []): void
    {
        $viewModel = AdminGigListingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigListing.php';
    }

    public function showAdminGigPosting(array $params = []): void
    {
        $viewModel = AdminGigPostingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    public function showAdminGigEditing(array $params = []): void
    {
        $viewModel = AdminGigEditingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
    }

}
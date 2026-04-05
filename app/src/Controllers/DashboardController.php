<?php

namespace App\Controllers;

use App\Enums\RoleType;
use App\Framework\Controller;
use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;
use App\ViewModels\AdminGigPostingViewModel;
use App\ViewModels\AdminGigEditingViewModel;
use App\ViewModels\AdminGigListingViewModel;

class DashboardController extends Controller
{
    /**
     * Route authenticated users to the correct dashboard by role.
     */
    public function showDashboard(array $params = []): void
    {
        $this->requireAuthentication();

        $role = $this->authUserRole();

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

    /**
     * Render the admin or production house dashboard.
     */
    public function showAdminDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminDashboardViewModel::createAdminDefault();

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    /**
     * Render the freelancer dashboard.
     */
    public function showFreelancerDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $viewModel = FreelancerDashboardViewModel::createFreelancerDefault();

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
    }

    /**
     * Render admin gig listing screen.
     */
    public function showAdminGigListing(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminGigListingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigListing.php';
    }

    /**
     * Render admin gig posting screen.
     */
    public function showAdminGigPosting(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminGigPostingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    /**
     * Render admin gig editing screen.
     */
    public function showAdminGigEditing(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminGigEditingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
    }

    /**
     * Destroy session and send user back to signin.
     */
    private function denyAndRedirectToSignin(): void
    {
        $this->destroySession();
        $this->redirect('/signin');
    }

}
<?php 

namespace App\Controllers;

use App\Enums\RoleType;
use App\Framework\Controller;
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

        $viewModel = AdminProfileViewModel::createDefault();

        include __DIR__ . '/../Views/profiles/adminProfile.php';
    }

    /**
     * Render the freelancer profile page.
     */
    public function showFreelancerProfile(): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $viewModel = FreelancerProfileViewModel::createDefault();

        include __DIR__ . '/../Views/profiles/freelancerProfile.php';
    }

}
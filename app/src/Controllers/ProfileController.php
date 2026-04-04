<?php 

namespace App\Controllers;

use App\ViewModels\AdminProfile;
use App\ViewModels\FreelancerProfile;

class ProfileController
{
    public function showAdminProfile()
    {
        $viewModel = AdminProfile::createDefault();

        include __DIR__ . '/../Views/profiles/adminProfile.php';
    }

    public function showFreelancerProfile()
    {
        $viewModel = FreelancerProfile::createDefault();

        include __DIR__ . '/../Views/profiles/freelancerProfile.php';
    }
}
<?php 

namespace App\Controllers;

use App\ViewModels\AdminProfileViewModel;
use App\ViewModels\FreelancerProfileViewModel;

class ProfileController
{
    public function showAdminProfile()
    {
        $viewModel = AdminProfileViewModel::createDefault();

        include __DIR__ . '/../Views/profiles/adminProfile.php';
    }

    public function showFreelancerProfile()
    {
        $viewModel = FreelancerProfileViewModel::createDefault();

        include __DIR__ . '/../Views/profiles/freelancerProfile.php';
    }
}
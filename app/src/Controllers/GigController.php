<?php

namespace App\Controllers;

use App\ViewModels\GigListingViewModel;
use App\ViewModels\GigDetailViewModel;

class GigController
{
    public function showGigListing(array $params = []): void
    {
        $viewModel = GigListingViewModel::createDefault();

        include __DIR__ . '/../Views/gigs/gigListing.php';
    }

    public function showGigDetail(array $params = []): void
    {
        $viewModel = GigDetailViewModel::createDefault();

        include __DIR__ . '/../Views/gigs/gigDetail.php';
    }
}
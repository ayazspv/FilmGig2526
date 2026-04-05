<?php

namespace App\Controllers;

use App\Config;
use App\Repositories\GigRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
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
        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            header('Location: /gigs');
            exit;
        }

        $gigRepository = new GigRepository(Config::pdo());
        $userRepository = new UserRepository(Config::pdo());
        $productionHouseRepository = new ProductionHouseRepository(Config::pdo());

        $gig = $gigRepository->findById($gigId);

        if ($gig === null) {
            header('Location: /gigs');
            exit;
        }

        $owner = $userRepository->findById($gig->getOwnerId());
        $productionHouse = $productionHouseRepository->findByUserId($gig->getOwnerId());

        $viewModel = GigDetailViewModel::createFromGig($gig, $owner, $productionHouse);

        include __DIR__ . '/../Views/gigs/gigDetail.php';
    }
}
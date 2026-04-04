<?php

namespace App\Controllers;

use App\ViewModels\HomeViewModel;

class HomeController
{
	/**
	 * Display the home page.
	 *
	 * @param array $params Route parameters (unused for this route)
	 */
	public function index(array $params = []): void
	{
		$viewModel = HomeViewModel::createDefault();

		$pageTitle = $viewModel->pageTitle;
		$hero = $viewModel->hero;
		$howItWorks = $viewModel->howItWorks;
		$gigs = $viewModel->gigs;
		$whyFilmGig = $viewModel->whyFilmGig;

		include __DIR__ . '/../Views/home.php';
	}
}

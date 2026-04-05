<?php

namespace App\Controllers;

use App\Config;
use App\Repositories\FreelancerRepository;
use App\Repositories\GigRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\SubmissionRepository;
use App\ViewModels\HomeViewModel;

class HomeController
{
	/**
	 * Render the home page.
	 */
	public function index(array $params = []): void
	{
		$pageTitle = 'Home - FilmGig';
		$homeApiEndpoint = '/api/home';

		include __DIR__ . '/../Views/home.php';
	}

	/**
	 * Return home page data as JSON for client-side rendering.
	 */
	public function apiIndex(array $params = []): void
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($this->buildHomeViewModel()->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Build the homepage view model from live repository data.
	 */
	private function buildHomeViewModel(): HomeViewModel
	{
		$gigRepository = new GigRepository(Config::pdo());
		$productionHouseRepository = new ProductionHouseRepository(Config::pdo());
		$freelancerRepository = new FreelancerRepository(Config::pdo());
		$submissionRepository = new SubmissionRepository(Config::pdo());

		$allGigs = $gigRepository->findAll();

		return HomeViewModel::createFromData(
			$this->findLatestActiveGigs($allGigs),
			$this->countActiveGigs($allGigs),
			count($productionHouseRepository->findAll()),
			count($freelancerRepository->findAll()),
			count($submissionRepository->findAll())
		);
	}

	/**
	 * Return the most recent active gigs for the homepage.
	 */
	private function findLatestActiveGigs(array $gigs): array
	{
		$activeGigs = array_values(array_filter($gigs, static fn($gig): bool => $gig->getStatus() === 'active'));

		usort(
			$activeGigs,
			static fn($left, $right): int => strcmp($right->getCreatedAt(), $left->getCreatedAt())
		);

		return array_slice($activeGigs, 0, 6);
	}

	/**
	 * Count all active gigs.
	 */
	private function countActiveGigs(array $gigs): int
	{
		return count(array_filter($gigs, static fn($gig): bool => $gig->getStatus() === 'active'));
	}
}

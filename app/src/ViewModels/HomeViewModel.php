<?php

namespace App\ViewModels;

class HomeViewModel
{
	public function __construct(
		public readonly string $pageTitle,
		public readonly array $hero,
		public readonly array $howItWorks,
		public readonly array $gigs,
		public readonly array $whyFilmGig,
	) {
	}

	public static function createDefault(): self
	{
		return new self(
			pageTitle: 'Home - FilmGig',
			hero: [
				'title' => "Find Your Next\nFilm Gig\nin Seconds!",
				'subtitle' => 'Browse thousands of gigs in film production, editing, and more.',
				'cta' => [
					'url' => '/signup',
					'label' => 'Click to sign up now!',
				],
				'searchPlaceholder' => 'Search for gigs',
				'searchButtonLabel' => 'Search',
				'image' => [
					'src' => 'images/hero-image.png',
					'alt' => 'Hero Image',
				],
			],
			howItWorks: [
				'title' => 'This is How it Works',
				'steps' => [
					['icon' => 'bi bi-person-circle', 'title' => 'Step 1', 'description' => 'Sign up for an account'],
					['icon' => 'bi bi-search', 'title' => 'Step 2', 'description' => 'Search for gigs'],
					['icon' => 'bi bi-file-earmark-text', 'title' => 'Step 3', 'description' => 'Apply for gigs'],
					['icon' => 'bi bi-briefcase', 'title' => 'Step 4', 'description' => 'Get hired!'],
				],
			],
			gigs: [
				'title' => 'Available Gigs',
				'items' => [
					['title' => 'Production Assistant', 'description' => 'Support on-set production logistics.', 'thumbnail' => 'thumbnail', 'applyUrl' => 'gig-details'],
					['title' => 'Video Editor', 'description' => 'Edit social and promo video content.', 'thumbnail' => 'thumbnail', 'applyUrl' => 'gig-details'],
					['title' => 'Camera Operator', 'description' => 'Operate cameras for event shoots.', 'thumbnail' => 'thumbnail', 'applyUrl' => 'gig-details'],
					['title' => 'Sound Recordist', 'description' => 'Capture clean on-location audio.', 'thumbnail' => 'thumbnail', 'applyUrl' => 'gig-details'],
					['title' => 'Lighting Technician', 'description' => 'Set up and manage lighting rigs.', 'thumbnail' => 'thumbnail', 'applyUrl' => 'gig-details'],
					['title' => 'Colorist', 'description' => 'Grade footage for final delivery.', 'thumbnail' => 'thumbnail', 'applyUrl' => 'gig-details'],
				],
			],
			whyFilmGig: [
				'title' => 'Why FilmGig?',
				'description' => 'FilmGig helps film professionals discover opportunities faster and apply with less friction.',
				'bullets' => [
					'Find gigs in seconds',
					'Apply for gigs with ease',
					'Get hired!',
				],
				'image' => [
					'src' => 'images/why-filmgig.png',
					'alt' => 'Why FilmGig',
				],
			],
		);
	}
}

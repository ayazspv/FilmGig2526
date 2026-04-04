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
				'title' => "Find Your Next\nFilm Gig\nIn Seconds",
				'subtitle' => 'Discover curated opportunities in production, post-production, camera, sound, and creative direction.',
				'cta' => [
					'url' => '/signup',
					'label' => 'Start Your Journey',
				],
				'searchPlaceholder' => 'Search role, location, or production type',
				'searchButtonLabel' => 'Search',
				'image' => [
					'src' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=900&q=80',
					'alt' => 'Film crew planning a scene',
				],
			],
			howItWorks: [
				'title' => 'How It Works',
				'steps' => [
					['icon' => 'fa-solid fa-user-plus', 'title' => 'Create Profile', 'description' => 'Show your skills, portfolio, and availability in minutes.'],
					['icon' => 'fa-solid fa-magnifying-glass', 'title' => 'Discover Gigs', 'description' => 'Browse relevant projects from trusted teams and studios.'],
					['icon' => 'fa-solid fa-paper-plane', 'title' => 'Apply Fast', 'description' => 'Send focused applications with your profile and reel attached.'],
					['icon' => 'fa-solid fa-clapperboard', 'title' => 'Work & Grow', 'description' => 'Get hired, build credits, and level up your film career.'],
				],
			],
			gigs: [
				'title' => 'Available Gigs',
				'items' => [
					['title' => 'Production Assistant', 'description' => 'Support call sheets, crew coordination, and location logistics for a branded shoot.', 'thumbnail' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=80', 'price' => '$180/day', 'applyUrl' => 'gig-details'],
					['title' => 'Video Editor', 'description' => 'Edit fast-paced short-form campaign videos with subtitles, transitions, and color balancing.', 'thumbnail' => 'https://images.unsplash.com/photo-1574717024453-3540562e8b2d?auto=format&fit=crop&w=900&q=80', 'price' => '$320/project', 'applyUrl' => 'gig-details'],
					['title' => 'Camera Operator', 'description' => 'Operate cinema camera setups for a two-day event production with multi-angle coverage.', 'thumbnail' => 'https://images.unsplash.com/photo-1497032628192-86f99bcd76bc?auto=format&fit=crop&w=900&q=80', 'price' => '$250/day', 'applyUrl' => 'gig-details'],
					['title' => 'Sound Recordist', 'description' => 'Capture clean interviews and ambient sound on documentary locations.', 'thumbnail' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=900&q=80', 'price' => '$210/day', 'applyUrl' => 'gig-details'],
					['title' => 'Lighting Technician', 'description' => 'Set up portable lighting rigs for studio product scenes and talent interviews.', 'thumbnail' => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=900&q=80', 'price' => '$240/day', 'applyUrl' => 'gig-details'],
					['title' => 'Colorist', 'description' => 'Color grade a 6-minute short film for festival submission and web distribution.', 'thumbnail' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=900&q=80', 'price' => '$400/project', 'applyUrl' => 'gig-details'],
				],
			],
			whyFilmGig: [
				'title' => 'Why FilmGig?',
				'description' => 'FilmGig helps film professionals discover relevant opportunities faster, connect with trusted productions, and apply without platform friction. We are built for creative momentum: less time searching, more time making great work.',
				'bullets' => [
					'Specialized roles from real productions, not generic listings',
					'A faster application flow designed for portfolio-based hiring',
					'Career-building opportunities for both new and experienced crew',
				],
				'image' => [
					'src' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80',
					'alt' => 'Filmmakers working outdoors',
				],
			],
		);
	}
}

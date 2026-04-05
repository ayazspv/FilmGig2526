<?php

namespace App\ViewModels;

use App\Enums\GigRateType;
use App\Models\Gig;

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

	public static function createFromData(array $gigs, int $activeGigCount, int $productionHouseCount, int $freelancerCount, int $submissionCount): self
	{
		$featuredGigs = array_map(static function (Gig $gig): array {
			$rateType = GigRateType::tryFrom($gig->getRateType());

			return [
				'title' => $gig->getTitle(),
				'description' => $gig->getDescription(),
				'thumbnail' => $gig->getImageUrl(),
				'price' => sprintf('EUR %.2f/%s', $gig->getPayRate(), $rateType?->suffix() ?? 'hr'),
				'applyUrl' => '/gigs/' . $gig->getGigId(),
			];
		}, array_slice($gigs, 0, 6));

		return new self(
			pageTitle: 'Home - FilmGig',
			hero: [
				'title' => "Find Your Next\nFilm Gig\nIn Seconds",
				'subtitle' => sprintf(
					'Discover %d active opportunities from %d production houses and a growing freelancer network.',
					$activeGigCount,
					$productionHouseCount
				),
				'cta' => [
					'url' => '/signup',
					'label' => 'Start Your Journey',
				],
				'searchPlaceholder' => 'Search role, location, or production type',
				'searchButtonLabel' => 'Search',
				'stats' => [
					['label' => 'Active Gigs', 'value' => (string) $activeGigCount, 'note' => 'Open opportunities'],
					['label' => 'Production Houses', 'value' => (string) $productionHouseCount, 'note' => 'Hiring on the platform'],
					['label' => 'Freelancers', 'value' => (string) $freelancerCount, 'note' => 'Creators in the network'],
					['label' => 'Applications', 'value' => (string) $submissionCount, 'note' => 'Submissions received'],
				],
				'image' => [
					'src' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=900&q=80',
					'alt' => 'Film crew planning a scene',
				],
			],
			howItWorks: [
				'title' => 'How It Works',
				'steps' => [
					['icon' => 'fa-solid fa-user-plus', 'title' => 'Create Profile', 'description' => 'Set up your public details and profile picture once, then reuse it for every application.'],
					['icon' => 'fa-solid fa-magnifying-glass', 'title' => 'Discover Gigs', 'description' => sprintf('Browse %d active gigs from production houses currently hiring.', $activeGigCount)],
					['icon' => 'fa-solid fa-paper-plane', 'title' => 'Apply Fast', 'description' => sprintf('Submit applications to any of the %d live opportunities directly from the gig page.', $activeGigCount)],
					['icon' => 'fa-solid fa-clapperboard', 'title' => 'Work & Grow', 'description' => 'Track outcomes, build credits, and keep your profile ready for the next brief.'],
				],
			],
			gigs: [
				'title' => 'Available Gigs',
				'subtitle' => sprintf('Browse the latest %d active gigs currently available.', $activeGigCount),
				'items' => $featuredGigs,
			],
			whyFilmGig: [
				'title' => 'Why FilmGig?',
				'description' => 'FilmGig helps film professionals discover relevant opportunities faster, connect with trusted productions, and apply without platform friction. It is built around the real activity happening on the platform right now.',
				'bullets' => [
					sprintf('%d active gigs are live and ready to browse', $activeGigCount),
					sprintf('%d production houses are currently hiring on FilmGig', $productionHouseCount),
					sprintf('%d freelancers already have profiles in the network', $freelancerCount),
				],
				'image' => [
					'src' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80',
					'alt' => 'Filmmakers working outdoors',
				],
			],
		);
	}

	public static function createDefault(): self
	{
		return self::createFromData([], 0, 0, 0, 0);
	}

	public static function createFromGigs(array $gigs): self
	{
		return self::createFromData($gigs, count($gigs), 0, 0, 0);
	}

	public function toArray(): array
	{
		return [
			'pageTitle' => $this->pageTitle,
			'hero' => $this->hero,
			'howItWorks' => $this->howItWorks,
			'gigs' => $this->gigs,
			'whyFilmGig' => $this->whyFilmGig,
		];
	}
}

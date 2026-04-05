<?php

namespace App\ViewModels;

use App\Enums\GigCategory;
use App\Enums\GigRateType;
use App\Models\Gig;

class GigListingViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $filters,
        public readonly array $categoryOptions,
        public readonly array $selectedCategories,
        public readonly array $gigs,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Gig Listing - FilmGig',
            badgeLabel: 'Gig Marketplace',
            heroTitle: 'Discover Film Industry Gigs',
            heroDescription: 'Browse the latest gigs and use filters to find opportunities that match your skill set.',
            filters: [
                'date' => '',
                'location' => '',
                'minimumRate' => 20,
            ],
            categoryOptions: GigCategory::filterOptions(),
            selectedCategories: [],
            gigs: [
                [
                    'title' => 'Documentary Camera Operator',
                    'description' => 'Capture behind-the-scenes footage for a 3-day documentary shoot.',
                    'rate' => 'EUR 55/hr',
                    'location' => 'Amsterdam, Netherlands',
                    'startTime' => '08:30',
                    'category' => 'Camera',
                    'image' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=900&q=80',
                    'detailUrl' => 'gig-detail',
                ],
                [
                    'title' => 'Short Film Assistant Producer',
                    'description' => 'Coordinate crew schedules and on-set logistics for an indie short.',
                    'rate' => 'EUR 48/hr',
                    'location' => 'Rotterdam, Netherlands',
                    'startTime' => '10:00',
                    'category' => 'Production',
                    'image' => 'https://images.unsplash.com/photo-1497032628192-86f99bcd76bc?auto=format&fit=crop&w=900&q=80',
                    'detailUrl' => 'gig-detail',
                ],
                [
                    'title' => 'Event Sound Technician',
                    'description' => 'Manage live sound setup for a two-evening studio showcase.',
                    'rate' => 'EUR 42/hr',
                    'location' => 'Utrecht, Netherlands',
                    'startTime' => '16:00',
                    'category' => 'Sound',
                    'image' => 'https://images.unsplash.com/photo-1516280030429-27679b3dc9cf?auto=format&fit=crop&w=900&q=80',
                    'detailUrl' => 'gig-detail',
                ],
                [
                    'title' => 'Commercial Video Editor',
                    'description' => 'Edit a set of social-first ad videos with quick turnaround.',
                    'rate' => 'EUR 60/hr',
                    'location' => 'The Hague, Netherlands',
                    'startTime' => '11:00',
                    'category' => 'Editing',
                    'image' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?auto=format&fit=crop&w=900&q=80',
                    'detailUrl' => 'gig-detail',
                ],
            ],
        );
    }

    public static function createFromGigs(array $gigs): self
    {
        $default = self::createDefault();

        $normalizedGigs = array_map(
            static fn(Gig $gig): array => [
                'title' => $gig->getTitle(),
                'description' => $gig->getDescription(),
                'rate' => sprintf(
                    'EUR %.2f/%s',
                    $gig->getPayRate(),
                    GigRateType::tryFrom($gig->getRateType())?->suffix() ?? 'hr'
                ),
                'location' => $gig->getLocation(),
                'startDate' => $gig->getStartDate(),
                'category' => GigCategory::tryFrom($gig->getCategory())?->label() ?? $gig->getCategory(),
                'imageUrl' => $gig->getImageUrl(),
                'detailUrl' => '/gigs/' . $gig->getGigId(),
            ],
            $gigs
        );

        $selectedCategories = [];

        return new self(
            pageTitle: $default->pageTitle,
            badgeLabel: $default->badgeLabel,
            heroTitle: $default->heroTitle,
            heroDescription: $default->heroDescription,
            filters: $default->filters,
            categoryOptions: $default->categoryOptions,
            selectedCategories: $selectedCategories,
            gigs: $normalizedGigs,
        );
    }
}
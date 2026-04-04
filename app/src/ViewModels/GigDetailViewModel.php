<?php

namespace App\ViewModels;

class GigDetailViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $gig,
        public readonly array $requirements,
        public readonly array $highlights,
        public readonly array $contact,
        public readonly array $relatedGigs,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Gig Detail - FilmGig',
            badgeLabel: 'Gig Detail',
            heroTitle: 'Documentary Camera Operator',
            heroDescription: 'Review the full gig brief, requirements, and compensation before applying.',
            gig: [
                'title' => 'Documentary Camera Operator',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Join a small crew to film interviews and b-roll for a 3-day documentary production focused on local creative entrepreneurs.',
                'rateType' => 'Hourly',
                'payRate' => 'EUR 55/hr',
                'duration' => '3 days',
                'location' => 'Amsterdam, Netherlands',
                'startDate' => '2026-04-12',
                'startTime' => '08:30',
                'category' => 'Camera',
            ],
            requirements: [
                '3+ years of camera operation experience',
                'Portfolio with documentary or interview work',
                'Comfortable working in small agile crews',
            ],
            highlights: [
                'Meals and local transport reimbursed',
                'Potential extension to a follow-up project',
                'Credit in final production release',
            ],
            contact: [
                'name' => 'Lotte van Dijk',
                'company' => 'Northlight Stories',
                'email' => 'lotte@northlightstories.nl',
            ],
            relatedGigs: [
                [
                    'title' => 'Second Camera Assistant',
                    'location' => 'Haarlem, Netherlands',
                    'rate' => 'EUR 38/hr',
                    'url' => 'gig-detail',
                ],
                [
                    'title' => 'Lighting Technician for Interviews',
                    'location' => 'Leiden, Netherlands',
                    'rate' => 'EUR 44/hr',
                    'url' => 'gig-detail',
                ],
            ],
        );
    }
}
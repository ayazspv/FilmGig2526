<?php

namespace App\ViewModels;

class AdminGigListingViewModel 
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $stats,
        public readonly array $primaryTable,
        public readonly array $secondaryList,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Admin Gig Listing - FilmGig',
            badgeLabel: 'Admin Gigs',
            heroTitle: 'Manage Published Gigs',
            heroDescription: 'Track performance and manage status for all currently published gigs.',
            stats: [
                ['label' => 'Active Gigs', 'value' => '14', 'note' => '+2 this week'],
                ['label' => 'Drafts', 'value' => '4', 'note' => 'Needs review'],
                ['label' => 'Applications', 'value' => '97', 'note' => '+11 today'],
                ['label' => 'Avg. Rate', 'value' => 'EUR 52/hr', 'note' => 'Across active gigs'],
            ],
            primaryTable: [
                'title' => 'Current Gig Openings',
                'columns' => ['Role', 'Status', 'Applications', 'Actions'],
                'rows' => [
                    ['title' => 'Documentary Camera Operator', 'status' => 'Active', 'value' => 22],
                    ['title' => 'Commercial Video Editor', 'status' => 'Active', 'value' => 31],
                    ['title' => 'Lighting Technician', 'status' => 'Paused', 'value' => 9],
                    ['title' => 'Assistant Producer', 'status' => 'Draft', 'value' => 0],
                ],
            ],
            secondaryList: [
                'title' => 'Recent Activity',
                'items' => [
                    ['title' => 'New applicant: Samira Jansen', 'subtitle' => 'Documentary Camera Operator', 'meta' => '10m ago'],
                    ['title' => 'Gig status updated', 'subtitle' => 'Lighting Technician set to Paused', 'meta' => '45m ago'],
                    ['title' => 'Draft saved', 'subtitle' => 'Assistant Producer', 'meta' => '2h ago'],
                    ['title' => 'Gig published', 'subtitle' => 'Commercial Video Editor', 'meta' => 'Yesterday'],
                ],
            ],
        );
    }
}
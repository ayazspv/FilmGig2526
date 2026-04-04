<?php

namespace App\ViewModels;

class AdminDashboardViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroDescription,
        public readonly array $stats,
        public readonly array $primaryTable,
        public readonly array $secondaryList,
    ) {
    }

    public static function createAdminDefault(): self
    {
        return new self(
            pageTitle: 'Admin Dashboard - FilmGig',
            badgeLabel: 'Admin Dashboard',
            heroDescription: 'Monitor platform activity and manage gig operations from one place.',
            stats: [
                ['label' => 'Active Gigs', 'value' => '28', 'note' => '+4 this week', 'id' => 'activeGigs'],
                ['label' => 'New Applicants', 'value' => '76', 'note' => '12 today', 'id' => 'openSubmissions'],
                ['label' => 'In Progress', 'value' => '14', 'note' => '3 near deadline', 'id' => 'openProjects'],
                ['label' => 'Resolved Reports', 'value' => '19', 'note' => 'Last 7 days'],
            ],
            primaryTable: [
                'title' => 'Current Gigs Overview',
                'columns' => ['Gig Name', 'Status', 'Applicants', 'Actions'],
                'rows' => [
                    ['title' => 'Documentary Camera Operator', 'status' => 'Open', 'value' => '12'],
                    ['title' => 'Commercial Video Editor', 'status' => 'Reviewing', 'value' => '24'],
                    ['title' => 'Lighting Technician - Studio', 'status' => 'In Progress', 'value' => '8'],
                    ['title' => 'Sound Recordist (On Location)', 'status' => 'Open', 'value' => '9'],
                ],
            ],
            secondaryList: [
                'title' => 'Recent Applications',
                'items' => [
                    ['title' => 'Sara van Dijk', 'subtitle' => 'Colorist', 'meta' => '5 min ago'],
                    ['title' => 'Jamal Kader', 'subtitle' => 'Camera Operator', 'meta' => '18 min ago'],
                    ['title' => 'Liam de Groot', 'subtitle' => 'Audio Engineer', 'meta' => '42 min ago'],
                    ['title' => 'Nina Bos', 'subtitle' => 'Production Assistant', 'meta' => '1 hour ago'],
                ],
            ],
        );
    }
}
<?php

namespace App\ViewModels;

class FreelancerDashboardViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroDescription,
        public readonly array $stats,
        public readonly array $applications,
        public readonly array $recommendedGigs,
        public readonly array $recentActions,
    ) {
    }

    public static function createFreelancerDefault(): self
    {
        return new self(
            pageTitle: 'Freelancer Dashboard - FilmGig',
            badgeLabel: 'Freelancer Dashboard',
            heroDescription: 'Track opportunities, applications, and your ongoing projects.',
            stats: [
                ['label' => 'Gigs Applied', 'value' => '16', 'note' => '+2 this week', 'id' => 'pendingSubmissions'],
                ['label' => 'Gigs in Progress', 'value' => '3', 'note' => '1 due this weekend'],
                ['label' => 'Completed Gigs', 'value' => '22', 'note' => 'This year'],
                ['label' => 'Earnings', 'value' => 'EUR 3,640', 'note' => 'Current month'],
            ],
            applications: [
                'title' => 'My Applications',
                'columns' => ['Gig Title', 'Status', 'Pay Type', 'Pay Rate', 'Actions'],
                'rows' => [
                    ['title' => 'Commercial Video Editor', 'status' => 'Shortlisted', 'payType' => 'Per project', 'payRate' => 'EUR 480'],
                    ['title' => 'Lighting Assistant', 'status' => 'Viewed', 'payType' => 'Per day', 'payRate' => 'EUR 190'],
                    ['title' => 'Camera Operator', 'status' => 'Interview Invite', 'payType' => 'Per day', 'payRate' => 'EUR 220'],
                    ['title' => 'Colorist', 'status' => 'Pending', 'payType' => 'Per project', 'payRate' => 'EUR 350'],
                ],
            ],
            recommendedGigs: [
                'title' => 'Recommended Gigs',
                'columns' => ['Title', 'Pay Type', 'Pay Rate', 'Actions'],
                'rows' => [
                    ['title' => 'Assistant Camera - Brand Shoot', 'status' => 'Per day', 'value' => 'EUR 220'],
                    ['title' => 'Freelance Video Editor (Short Ads)', 'status' => 'Per project', 'value' => 'EUR 480'],
                    ['title' => 'Sound Mix Assistant', 'status' => 'Per day', 'value' => 'EUR 190'],
                    ['title' => 'BTS Photographer', 'status' => 'Per project', 'value' => 'EUR 350'],
                ],
            ],
            recentActions: [
                'title' => 'Recent Application Updates',
                'items' => [
                    ['title' => 'Commercial Video Editor', 'subtitle' => 'Status: Shortlisted', 'meta' => '2 hours ago'],
                    ['title' => 'Lighting Assistant', 'subtitle' => 'Status: Viewed', 'meta' => 'Yesterday'],
                    ['title' => 'Camera Operator', 'subtitle' => 'Status: Interview Invite', 'meta' => '2 days ago'],
                    ['title' => 'Colorist', 'subtitle' => 'Status: Pending', 'meta' => '3 days ago'],
                ],
            ],
        );
    }
}
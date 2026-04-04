<?php

namespace App\ViewModels;

class DashboardViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroDescription,
        public readonly array $stats,
        public readonly array $primaryTable,
        public readonly array $secondaryList,
        public readonly array $focus,
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
            focus: [
                'leftTitle' => 'Talent Suggestions',
                'leftDescription' => 'Top-rated professionals available this week.',
                'leftItems' => [
                    ['title' => 'Ava Jansen', 'subtitle' => 'Steadicam', 'badge' => 'Rating 4.9'],
                    ['title' => 'Milan Vermeer', 'subtitle' => 'Set Lighting', 'badge' => 'Rating 4.8'],
                    ['title' => 'Yara Peters', 'subtitle' => 'Post-Production', 'badge' => 'Rating 4.9'],
                ],
                'rightTitle' => 'Admin Focus',
                'rightDescription' => 'Priority tasks for today.',
                'badges' => ['Review flagged gigs', 'Approve 6 profiles', 'Close 2 reports'],
                'buttonLabel' => 'Open Admin Queue',
            ],
        );
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
            primaryTable: [
                'title' => 'Recommended Gigs',
                'columns' => ['Title', 'Pay Type', 'Pay Rate', 'Actions'],
                'rows' => [
                    ['title' => 'Assistant Camera - Brand Shoot', 'status' => 'Per day', 'value' => 'EUR 220'],
                    ['title' => 'Freelance Video Editor (Short Ads)', 'status' => 'Per project', 'value' => 'EUR 480'],
                    ['title' => 'Sound Mix Assistant', 'status' => 'Per day', 'value' => 'EUR 190'],
                    ['title' => 'BTS Photographer', 'status' => 'Per project', 'value' => 'EUR 350'],
                ],
            ],
            secondaryList: [
                'title' => 'Recent Application Updates',
                'items' => [
                    ['title' => 'Commercial Video Editor', 'subtitle' => 'Status: Shortlisted', 'meta' => '2 hours ago'],
                    ['title' => 'Lighting Assistant', 'subtitle' => 'Status: Viewed', 'meta' => 'Yesterday'],
                    ['title' => 'Camera Operator', 'subtitle' => 'Status: Interview Invite', 'meta' => '2 days ago'],
                    ['title' => 'Colorist', 'subtitle' => 'Status: Pending', 'meta' => '3 days ago'],
                ],
            ],
            focus: [
                'leftTitle' => 'Skill Match Insights',
                'leftDescription' => 'Roles currently matching your top profile skills.',
                'leftItems' => [
                    ['title' => 'Camera Operation', 'subtitle' => 'Match confidence: High', 'badge' => '94%'],
                    ['title' => 'Editing Workflow', 'subtitle' => 'Match confidence: High', 'badge' => '91%'],
                    ['title' => 'Set Lighting', 'subtitle' => 'Match confidence: Medium', 'badge' => '78%'],
                ],
                'rightTitle' => 'Application Focus',
                'rightDescription' => 'Prioritize roles that match your profile strength for better response rates.',
                'badges' => ['Prioritize high-match gigs', 'Follow up on shortlists', 'Refresh showreel links'],
                'buttonLabel' => 'Open Recommended Queue',
            ],
        );
    }
}
<?php

namespace App\ViewModels;

class FreelancerProfileViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly string $profileImage,
        public readonly array $summary,
        public readonly array $contact,
        public readonly array $personal,
        public readonly array $nichePreferences,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Freelancer Profile - FilmGig',
            badgeLabel: 'Freelancer Profile',
            heroTitle: 'Samira Jansen',
            heroDescription: 'Public profile details and freelancer specialization.',
            profileImage: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=220&q=80',
            summary: [
                'name' => 'Samira Jansen',
                'username' => 'camera_pro_nl',
                'role' => 'Freelancer',
            ],
            contact: [
                'email' => 'samira@filmgig.nl',
                'phone' => '+31 6 9876 1234',
                'address' => 'Utrecht Creative District, NL',
            ],
            personal: [
                'dateOfBirth' => '1996-09-14',
                'bio' => 'Freelance camera operator and editor with 5+ years of experience in commercial and documentary projects.',
            ],
            nichePreferences: ['Camera Operation', 'Documentary', 'Post-Production'],
        );
    }
}
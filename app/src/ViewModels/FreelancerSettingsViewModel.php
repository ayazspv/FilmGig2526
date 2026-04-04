<?php

namespace App\ViewModels;

class FreelancerSettingsViewModel {

    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly string $profileImage,
        public readonly array $form,
        public readonly array $nicheOptions,
        public readonly array $selectedNiches,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Freelancer Settings - FilmGig',
            badgeLabel: 'Freelancer Settings',
            heroTitle: 'Manage Profile Settings',
            heroDescription: 'Keep your personal details and niche preferences updated for better gig matches.',
            profileImage: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=220&q=80',
            form: [
                'username' => 'camera_pro_nl',
                'fullName' => 'Ayaz Jansen',
                'email' => 'samira@filmgig.nl',
                'address' => 'Utrecht Creative District, NL',
                'dateOfBirth' => '1996-09-14',
                'bio' => 'Freelance camera operator and editor with 5+ years of experience in commercial and documentary projects.',
            ],
            nicheOptions: [
                'camera' => 'Camera Operation',
                'editing' => 'Post-Production',
                'lighting' => 'Lighting',
                'sound' => 'Sound',
                'documentary' => 'Documentary',
            ],
            selectedNiches: ['camera', 'editing', 'documentary'],
        );
    }
}
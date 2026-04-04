<?php

namespace App\ViewModels;

class AdminSettingsViewModel {

    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly string $profileImage,
        public readonly array $form,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Admin Settings - FilmGig',
            badgeLabel: 'Admin Settings',
            heroTitle: 'Manage Account Settings',
            heroDescription: 'Update company details, profile information, and platform preferences.',
            profileImage: 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?auto=format&fit=crop&w=220&q=80',
            form: [
                'username' => 'filmgig_admin',
                'companyName' => 'FilmGig Studios BV',
                'contactName' => 'Alex de Vries',
                'email' => 'admin@filmgig.nl',
                'address' => 'Rotterdam Media Park, NL',
                'foundedDate' => '2019-05-01',
                'bio' => 'FilmGig Studios connects top-tier production teams with trusted freelance talent across the Netherlands.',
            ],
        );
    }
}
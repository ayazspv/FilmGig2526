<?php

namespace App\ViewModels;

class AdminProfileViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly string $profileImage,
        public readonly array $summary,
        public readonly array $contact,
        public readonly string $about,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Admin Profile - FilmGig',
            badgeLabel: 'Company Profile',
            heroTitle: 'FilmGig Studios BV',
            heroDescription: 'Company profile and core contact information.',
            profileImage: 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?auto=format&fit=crop&w=220&q=80',
            summary: [
                'name' => 'FilmGig Studios BV',
                'username' => 'filmgig_admin',
                'market' => 'Film Production Marketplace',
            ],
            contact: [
                'contactPerson' => 'Alex de Vries',
                'email' => 'admin@filmgig.nl',
                'phone' => '+31 10 123 4567',
                'address' => 'Rotterdam Media Park, NL',
            ],
            about: 'FilmGig Studios helps productions and freelancers connect faster through a niche-focused hiring platform for the film industry.',
        );
    }
}
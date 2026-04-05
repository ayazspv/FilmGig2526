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
        public readonly array $form,
        public readonly array $errors,
        public readonly ?string $successMessage,
    ) {
    }

    public static function createFromData(array $profileData, array $errors = [], ?string $successMessage = null): self
    {
        $form = $profileData['form'] ?? [];

        return new self(
            pageTitle: 'Freelancer Profile - FilmGig',
            badgeLabel: 'Freelancer Profile',
            heroTitle: (string) ($form['fullName'] ?? 'Freelancer Profile'),
            heroDescription: 'Update your personal information and profile picture.',
            profileImage: (string) ($profileData['profileImage'] ?? ''),
            form: [
                'username' => (string) ($form['username'] ?? ''),
                'fullName' => (string) ($form['fullName'] ?? ''),
                'email' => (string) ($form['email'] ?? ''),
                'address' => (string) ($form['address'] ?? ''),
                'dateOfBirth' => (string) ($form['dateOfBirth'] ?? ''),
                'bio' => (string) ($form['bio'] ?? ''),
            ],
            errors: $errors,
            successMessage: $successMessage,
        );
    }
}
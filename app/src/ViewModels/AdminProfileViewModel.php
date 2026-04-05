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
        public readonly array $form,
        public readonly array $errors,
        public readonly ?string $successMessage,
    ) {
    }

    public static function createFromData(array $profileData, array $errors = [], ?string $successMessage = null): self
    {
        $form = $profileData['form'] ?? [];

        return new self(
            pageTitle: 'Admin Profile - FilmGig',
            badgeLabel: 'Company Profile',
            heroTitle: (string) ($form['companyName'] ?? 'Company Profile'),
            heroDescription: 'Update your company details, contact information, and profile picture.',
            profileImage: (string) ($profileData['profileImage'] ?? ''),
            form: [
                'username' => (string) ($form['username'] ?? ''),
                'companyName' => (string) ($form['companyName'] ?? ''),
                'contactName' => (string) ($form['contactName'] ?? ''),
                'email' => (string) ($form['email'] ?? ''),
                'address' => (string) ($form['address'] ?? ''),
                'website' => (string) ($form['website'] ?? ''),
                'bio' => (string) ($form['bio'] ?? ''),
            ],
            errors: $errors,
            successMessage: $successMessage,
        );
    }
}
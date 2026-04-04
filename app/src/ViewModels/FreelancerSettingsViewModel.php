<?php

namespace App\ViewModels;

class FreelancerSettingsViewModel {

    public function __construct(
        public readonly string $pageTitle,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Freelancer Settings - FilmGig',
        );
    }
}
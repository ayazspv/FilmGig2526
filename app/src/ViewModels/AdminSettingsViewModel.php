<?php

namespace App\ViewModels;

class AdminSettingsViewModel {

    public function __construct(
        public readonly string $pageTitle,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Admin Settings - FilmGig',
        );
    }
}
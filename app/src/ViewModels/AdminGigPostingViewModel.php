<?php

namespace App\ViewModels;

class AdminGigPostingViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $formFields,
        public readonly array $defaultValues,
        public readonly array $errors = [],
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Post a New Gig - FilmGig',
            badgeLabel: 'Create Gig',
            heroTitle: 'Post a New Gig Opening',
            heroDescription: 'Publish a detailed gig brief to attract the right freelancers quickly.',
            formFields: [
                ['name' => 'title', 'label' => 'Gig Title', 'type' => 'text', 'placeholder' => 'e.g. Camera Operator for Short Film'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'placeholder' => 'Provide a detailed description of the gig...'],
                ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => ['Camera', 'Editing', 'Sound', 'Production', 'Animation']],
                ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'placeholder' => 'e.g. Amsterdam, Netherlands'],
                ['name' => 'startDate', 'label' => 'Start Date', 'type' => 'date'],
                ['name' => 'rateType', 'label' => 'Rate Type', 'type' => 'select', 'options' => ['hourly', 'fixed']],
                ['name' => 'payRate', 'label' => 'Pay Rate (EUR)', 'type' => 'number', 'placeholder' => 50],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active', 'closed']],
            ],
            defaultValues: [
                'title' => '',
                'description' => '',
                'category' => 'Camera',
                'location' => '',
                'startDate' => '',
                'rateType' => 'hourly',
                'payRate' => '50',
                'status' => 'active',
            ],
        );
    }

    public static function createWithInput(array $oldInput, array $errors = []): self
    {
        $viewModel = self::createDefault();

        return new self(
            pageTitle: $viewModel->pageTitle,
            badgeLabel: $viewModel->badgeLabel,
            heroTitle: $viewModel->heroTitle,
            heroDescription: $viewModel->heroDescription,
            formFields: $viewModel->formFields,
            defaultValues: array_merge($viewModel->defaultValues, $oldInput),
            errors: $errors,
        );
    }
}
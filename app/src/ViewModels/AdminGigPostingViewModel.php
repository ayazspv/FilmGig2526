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
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Post a New Gig - Admin Dashboard',
            badgeLabel: 'Create Gig',
            heroTitle: 'Post a New Gig Opening',
            heroDescription: 'Publish a detailed gig brief to attract the right freelancers quickly.',
            formFields: [
                ['name' => 'title', 'label' => 'Gig Title', 'type' => 'text', 'placeholder' => 'e.g. Camera Operator for Short Film'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'placeholder' => 'Provide a detailed description of the gig...'],
                ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => ['Camera', 'Editing', 'Sound', 'Production', 'Animation']],
                ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'placeholder' => 'e.g. Amsterdam, Netherlands'],
                ['name' => 'startDate', 'label' => 'Start Date', 'type' => 'date'],
                ['name' => 'startTime', 'label' => 'Start Time', 'type' => 'time'],
                ['name' => 'rateType', 'label' => 'Rate Type', 'type' => 'select', 'options' => ['Hourly', 'Daily', 'Project']],
                ['name' => 'payRate', 'label' => 'Pay Rate (EUR)', 'type' => 'number', 'placeholder' => 50],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Active', 'Paused', 'Draft']],
            ],
            defaultValues: [
                'title' => '',
                'description' => '',
                'category' => 'Camera',
                'location' => '',
                'startDate' => '',
                'startTime' => '',
                'rateType' => 'Hourly',
                'payRate' => '50',
                'status' => 'Active',
            ],
        );
    }
}
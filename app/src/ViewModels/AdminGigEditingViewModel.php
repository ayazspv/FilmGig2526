<?php

namespace App\ViewModels;

class AdminGigEditingViewModel 
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $formFields,
        public readonly array $gigData,
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Edit Gig - Admin Dashboard',
            badgeLabel: 'Edit Gig',
            heroTitle: 'Update Existing Gig',
            heroDescription: 'Adjust status, rates, and details to keep your gig posting up to date.',
            formFields: [
                ['name' => 'title', 'label' => 'Gig Title', 'type' => 'text', 'placeholder' => 'e.g. Camera Operator for Short Film'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'placeholder' => 'Provide a detailed description of the gig...'],
                ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => ['Camera', 'Editing', 'Sound', 'Production', 'Animation']],
                ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'placeholder' => 'e.g. Amsterdam, Netherlands'],
                ['name' => 'startDate', 'label' => 'Start Date', 'type' => 'date'],
                ['name' => 'startTime', 'label' => 'Start Time', 'type' => 'time'],
                ['name' => 'rateType', 'label' => 'Rate Type', 'type' => 'select', 'options' => ['Hourly', 'Daily', 'Project']],
                ['name' => 'payRate', 'label' => 'Pay Rate (EUR)', 'type' => 'number', 'placeholder' => 50],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['Active', 'Paused', 'Draft', 'Closed']],
            ],
            gigData: [
                'title' => 'Documentary Camera Operator',
                'description' => 'Capture interviews and b-roll footage for a 3-day documentary production.',
                'category' => 'Camera',
                'location' => 'Amsterdam, Netherlands',
                'startDate' => '2026-04-12',
                'startTime' => '08:30',
                'rateType' => 'Hourly',
                'payRate' => '55',
                'status' => 'Active',
            ],
        );
    }
}
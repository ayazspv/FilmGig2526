<?php

namespace App\ViewModels;

use App\Enums\GigCategory;
use App\Enums\GigRateType;
use App\Enums\GigStatus;
use App\Models\Gig;

class AdminGigEditingViewModel 
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $formFields,
        public readonly array $gigData,
        public readonly array $errors = [],
    ) {
    }

    public static function createDefault(): self
    {
        return new self(
            pageTitle: 'Edit Gig - FilmGig',
            badgeLabel: 'Edit Gig',
            heroTitle: 'Update Existing Gig',
            heroDescription: 'Adjust status, rates, and details to keep your gig posting up to date.',
            formFields: [
                ['name' => 'image', 'label' => 'Gig Picture', 'type' => 'file', 'help' => 'Upload a new picture to replace the current one.'],
                ['name' => 'title', 'label' => 'Gig Title', 'type' => 'text', 'placeholder' => 'e.g. Camera Operator for Short Film'],
                ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'placeholder' => 'Provide a detailed description of the gig...'],
                ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => GigCategory::selectOptions()],
                ['name' => 'location', 'label' => 'Location', 'type' => 'text', 'placeholder' => 'e.g. Amsterdam, Netherlands'],
                ['name' => 'startDate', 'label' => 'Start Date', 'type' => 'date'],
                ['name' => 'rateType', 'label' => 'Rate Type', 'type' => 'select', 'options' => GigRateType::options()],
                ['name' => 'payRate', 'label' => 'Pay Rate (EUR)', 'type' => 'number', 'placeholder' => 50],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => GigStatus::options()],
            ],
            gigData: [
                'gigId' => 0,
                'imageUrl' => '',
                'title' => '',
                'description' => '',
                'category' => GigCategory::CAMERA->value,
                'location' => '',
                'startDate' => '',
                'rateType' => GigRateType::HOURLY->value,
                'payRate' => '50',
                'status' => GigStatus::ACTIVE->value,
            ],
        );
    }

    public static function createWithGig(Gig $gig, array $oldInput = [], array $errors = []): self
    {
        $viewModel = self::createDefault();

        $gigData = [
            'gigId' => $gig->getGigId(),
            'imageUrl' => $gig->getImageUrl(),
            'title' => $gig->getTitle(),
            'description' => $gig->getDescription(),
            'category' => $gig->getCategory(),
            'location' => $gig->getLocation(),
            'startDate' => $gig->getStartDate(),
            'rateType' => $gig->getRateType(),
            'payRate' => (string) $gig->getPayRate(),
            'status' => $gig->getStatus(),
        ];

        return new self(
            pageTitle: $viewModel->pageTitle,
            badgeLabel: $viewModel->badgeLabel,
            heroTitle: $viewModel->heroTitle,
            heroDescription: $viewModel->heroDescription,
            formFields: $viewModel->formFields,
            gigData: array_merge($gigData, $oldInput),
            errors: $errors,
        );
    }
}
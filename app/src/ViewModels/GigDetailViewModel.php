<?php

namespace App\ViewModels;

use App\Enums\GigRateType;
use App\Models\Gig;
use App\Models\ProductionHouse;
use App\Models\User;

class GigDetailViewModel
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $gig,
        public readonly array $contact,
        public readonly array $metadata,
    ) {
    }

    public static function createFromGig(Gig $gig, ?User $owner = null, ?ProductionHouse $productionHouse = null): self
    {
        $rateType = GigRateType::tryFrom($gig->getRateType());
        $rateTypeLabel = $rateType?->label() ?? ucfirst($gig->getRateType());
        $statusLabel = ucfirst($gig->getStatus());
        $companyName = $productionHouse !== null && $productionHouse->getCompanyName() !== ''
            ? $productionHouse->getCompanyName()
            : ($owner !== null ? $owner->getName() : 'Production House');

        return new self(
            pageTitle: $gig->getTitle() . ' - FilmGig',
            badgeLabel: 'Gig Detail',
            heroTitle: $gig->getTitle(),
            heroDescription: $gig->getDescription(),
            gig: [
                'gigId' => $gig->getGigId(),
                'title' => $gig->getTitle(),
                'imageUrl' => $gig->getImageUrl(),
                'description' => $gig->getDescription(),
                'category' => $gig->getCategory(),
                'location' => $gig->getLocation(),
                'startDate' => $gig->getStartDate(),
                'rateType' => $rateTypeLabel,
                'payRate' => sprintf('EUR %.2f', $gig->getPayRate()),
                'status' => $statusLabel,
            ],
            contact: [
                'name' => $owner !== null ? $owner->getName() : $companyName,
                'company' => $companyName,
                'email' => $owner !== null ? $owner->getEmail() : '',
                'website' => $productionHouse !== null ? $productionHouse->getWebsite() : '',
            ],
            metadata: [
                ['label' => 'Category', 'value' => $gig->getCategory()],
                ['label' => 'Location', 'value' => $gig->getLocation()],
                ['label' => 'Start Date', 'value' => $gig->getStartDate()],
                ['label' => 'Rate Type', 'value' => $rateTypeLabel],
                ['label' => 'Pay Rate', 'value' => sprintf('EUR %.2f', $gig->getPayRate())],
                ['label' => 'Status', 'value' => $statusLabel],
            ],
        );
    }
}
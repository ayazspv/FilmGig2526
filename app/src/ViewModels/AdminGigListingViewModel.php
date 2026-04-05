<?php

namespace App\ViewModels;

use App\Models\Gig;

class AdminGigListingViewModel 
{
    public function __construct(
        public readonly string $pageTitle,
        public readonly string $badgeLabel,
        public readonly string $heroTitle,
        public readonly string $heroDescription,
        public readonly array $stats,
        public readonly array $primaryTable,
        public readonly array $secondaryList,
    ) {
    }

    public static function createForGigs(array $gigs, string $ownerName): self
    {
        $totalGigs = count($gigs);
        $activeGigs = count(array_filter($gigs, static fn(Gig $gig): bool => $gig->getStatus() === 'active'));
        $closedGigs = count(array_filter($gigs, static fn(Gig $gig): bool => $gig->getStatus() === 'closed'));
        $averageRate = $totalGigs > 0
            ? array_sum(array_map(static fn(Gig $gig): float => $gig->getPayRate(), $gigs)) / $totalGigs
            : 0.0;

        return new self(
            pageTitle: 'Production House Gigs - FilmGig',
            badgeLabel: 'Production House Gigs',
            heroTitle: sprintf('%s, manage your gigs', $ownerName),
            heroDescription: 'Review every gig attached to your production house account, then edit or inspect each posting from one place.',
            stats: [
                ['label' => 'Total Gigs', 'value' => (string) $totalGigs, 'note' => 'All gigs in your account'],
                ['label' => 'Active', 'value' => (string) $activeGigs, 'note' => 'Currently open'],
                ['label' => 'Closed', 'value' => (string) $closedGigs, 'note' => 'Already finished'],
                ['label' => 'Avg. Rate', 'value' => $totalGigs > 0 ? sprintf('EUR %.2f/hr', $averageRate) : 'EUR 0.00/hr', 'note' => 'Across your gigs'],
            ],
            primaryTable: [
                'title' => 'Your Posted Gigs',
                'columns' => ['Gig', 'Category', 'Status', 'Rate', 'Actions'],
                'rows' => array_map(
                    static fn(Gig $gig): array => [
                        'title' => $gig->getTitle(),
                        'category' => $gig->getCategory(),
                        'status' => ucfirst($gig->getStatus()),
                        'value' => sprintf('EUR %.2f', $gig->getPayRate()),
                        'viewUrl' => '/gigs/' . $gig->getGigId(),
                        'editUrl' => '/dashboard/gigs/' . $gig->getGigId(),
                    ],
                    $gigs
                ),
            ],
            secondaryList: [
                'title' => 'Gig Summary',
                'items' => [
                    ['title' => 'Gig ownership', 'subtitle' => sprintf('%d gig(s) linked to %s', $totalGigs, $ownerName), 'meta' => 'Current account'],
                    ['title' => 'Active listings', 'subtitle' => sprintf('%d open gig(s) waiting for freelancers', $activeGigs), 'meta' => 'Live now'],
                    ['title' => 'Closed listings', 'subtitle' => sprintf('%d gig(s) already closed', $closedGigs), 'meta' => 'Archive'],
                ],
            ],
        );
    }
}
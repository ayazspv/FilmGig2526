<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Framework\Service;
use App\Repositories\GigRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
use App\ViewModels\GigDetailViewModel;

class GigReadService extends Service
{
    private GigRepository $gigRepository;
    private UserRepository $userRepository;
    private ProductionHouseRepository $productionHouseRepository;
    private SubmissionService $submissionService;

    /**
     * Build repository dependencies for gig read operations.
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->gigRepository = new GigRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
        $this->productionHouseRepository = new ProductionHouseRepository($pdo);
        $this->submissionService = new SubmissionService($pdo);
    }

    /**
     * Build detail-page data for one gig.
     */
    public function buildGigDetailData(int $gigId, string $authRole, int $authUserId): ?array
    {
        $gig = $this->gigRepository->findById($gigId);

        if ($gig === null) {
            return null;
        }

        $owner = $this->userRepository->findById($gig->getOwnerId());
        $productionHouse = $this->productionHouseRepository->findByUserId($gig->getOwnerId());

        return [
            'viewModel' => GigDetailViewModel::createFromGig($gig, $owner, $productionHouse),
            'hasAlreadyApplied' => $this->hasFreelancerApplied($gigId, $authRole, $authUserId),
        ];
    }

    /**
     * Return API-ready gig listing data with query filters applied.
     */
    public function getFilteredActiveGigs(array $query): array
    {
        $location = trim((string) ($query['location'] ?? ''));
        $date = trim((string) ($query['date'] ?? ''));
        $minimumRate = (float) ($query['minimumRate'] ?? 0);
        $categories = $this->normalizeCategories((string) ($query['categories'] ?? ''));

        $filteredGigs = array_values(array_filter(
            $this->gigRepository->findAll(),
            fn($gig): bool => $this->matchesGigFilters($gig, $location, $date, $minimumRate, $categories)
        ));

        return array_map(static fn($gig): array => [
            'gigId' => $gig->getGigId(),
            'title' => $gig->getTitle(),
            'description' => $gig->getDescription(),
            'category' => $gig->getCategory(),
            'location' => $gig->getLocation(),
            'startDate' => $gig->getStartDate(),
            'payRate' => $gig->getPayRate(),
            'rateType' => $gig->getRateType(),
            'imageUrl' => $gig->getImageUrl(),
            'status' => $gig->getStatus(),
            'detailUrl' => '/gigs/' . $gig->getGigId(),
        ], $filteredGigs);
    }

    /**
     * Determine whether the authenticated freelancer already applied to this gig.
     */
    private function hasFreelancerApplied(int $gigId, string $authRole, int $authUserId): bool
    {
        if ($authRole !== RoleType::FREELANCER->value || $authUserId <= 0) {
            return false;
        }

        $submissions = $this->submissionService->getFreelancerSubmissions($authUserId);

        foreach ($submissions as $submission) {
            if ((int) ($submission['gigId'] ?? 0) === $gigId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize category filters from query string input.
     */
    private function normalizeCategories(string $rawCategories): array
    {
        if ($rawCategories === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            fn(string $category): string => $this->normalizeCategory($category),
            explode(',', $rawCategories)
        )));
    }

    /**
     * Normalize category aliases to canonical values.
     */
    private function normalizeCategory(string $category): string
    {
        return match (strtolower(trim($category))) {
            'audio' => 'sound',
            default => strtolower(trim($category)),
        };
    }

    /**
     * Evaluate whether a gig satisfies listing API filters.
     */
    private function matchesGigFilters($gig, string $location, string $date, float $minimumRate, array $categories): bool
    {
        if (strtolower((string) $gig->getStatus()) !== 'active') {
            return false;
        }

        if ($location !== '' && stripos($gig->getLocation(), $location) === false) {
            return false;
        }

        if ($date !== '' && $gig->getStartDate() < $date) {
            return false;
        }

        if ($minimumRate > 0 && $gig->getPayRate() < $minimumRate) {
            return false;
        }

        if (!empty($categories) && !in_array($this->normalizeCategory($gig->getCategory()), $categories, true)) {
            return false;
        }

        return true;
    }
}

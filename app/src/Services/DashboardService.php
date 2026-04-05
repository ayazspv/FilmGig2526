<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Enums\SubmissionStatus;
use App\Framework\Service;
use App\Models\Gig;
use App\Models\Submission;
use App\Repositories\FreelancerRepository;
use App\Repositories\GigRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\SubmissionRepository;
use App\Repositories\UserRepository;
use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;

class DashboardService extends Service
{
    private GigRepository $gigRepository;
    private SubmissionRepository $submissionRepository;
    private FreelancerRepository $freelancerRepository;
    private ProductionHouseRepository $productionHouseRepository;
    private UserRepository $userRepository;

    /**
     * Build repository dependencies for dashboard read models.
     */
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
        $this->gigRepository = new GigRepository($pdo);
        $this->submissionRepository = new SubmissionRepository($pdo);
        $this->freelancerRepository = new FreelancerRepository($pdo);
        $this->productionHouseRepository = new ProductionHouseRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
    }

    /**
     * Build dashboard payload for the authenticated role.
     */
    public function buildPayload(string $role, int $userId): array
    {
        if ($role === RoleType::FREELANCER->value) {
            return $this->buildFreelancerPayload($userId);
        }

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            return $this->buildProductionHousePayload($role, $userId);
        }

        return [];
    }

    /**
     * Build admin/production dashboard payload.
     */
    private function buildProductionHousePayload(string $role, int $userId): array
    {
        $gigs = $role === RoleType::ADMIN->value
            ? $this->gigRepository->findAll()
            : $this->gigRepository->findByOwnerId($userId);

        $submissions = $role === RoleType::ADMIN->value
            ? $this->submissionRepository->findAll()
            : $this->collectSubmissionsForGigs($gigs);

        usort($gigs, static fn(Gig $left, Gig $right): int => strcmp($right->getCreatedAt(), $left->getCreatedAt()));
        usort($submissions, static fn(Submission $left, Submission $right): int => strcmp($right->getSubmittedAt(), $left->getSubmittedAt()));

        return AdminDashboardViewModel::createFromData(
            pageTitle: $role === RoleType::ADMIN->value ? 'Admin Dashboard - FilmGig' : 'Production House Dashboard - FilmGig',
            badgeLabel: $role === RoleType::ADMIN->value ? 'Admin Dashboard' : 'Production House Dashboard',
            heroDescription: $this->resolveProductionHeroDescription($role),
            stats: $this->buildProductionStats($gigs, $submissions, $role),
            primaryTable: [
                'title' => 'Current Gigs Overview',
                'columns' => ['Gig Name', 'Status', 'Applicants', 'Actions'],
                'rows' => $this->buildProductionPrimaryRows($gigs, $submissions, $role),
            ],
            secondaryList: [
                'title' => 'Recent Applications',
                'items' => $this->buildProductionSecondaryItems($submissions),
            ],
        )->toArray();
    }

    /**
     * Build freelancer dashboard payload.
     */
    private function buildFreelancerPayload(int $userId): array
    {
        $freelancer = $this->freelancerRepository->findByUserId($userId);

        if ($freelancer === null) {
            return $this->buildMissingFreelancerPayload();
        }

        $submissions = $this->submissionRepository->findByFreelancerId($freelancer->getFreelancerId());
        usort($submissions, static fn(Submission $left, Submission $right): int => strcmp($right->getSubmittedAt(), $left->getSubmittedAt()));

        $availableGigs = $this->findAvailableGigsForFreelancer($submissions);
        $stats = $this->buildFreelancerStats($submissions);
        $applications = $this->buildFreelancerApplicationsSection($submissions);
        $recommended = $this->buildFreelancerRecommendedSection($availableGigs);
        $recentActions = $this->buildFreelancerRecentActionsSection($submissions);

        return FreelancerDashboardViewModel::createFromData(
            pageTitle: 'Freelancer Dashboard - FilmGig',
            badgeLabel: 'Freelancer Dashboard',
            heroDescription: 'Track opportunities, applications, and your ongoing projects.',
            stats: $stats,
            applications: $applications,
            recommendedGigs: $recommended,
            recentActions: $recentActions,
        )->toArray();
    }

    /**
     * Return fallback payload when freelancer profile does not exist.
     */
    private function buildMissingFreelancerPayload(): array
    {
        return FreelancerDashboardViewModel::createFromData(
            pageTitle: 'Freelancer Dashboard - FilmGig',
            badgeLabel: 'Freelancer Dashboard',
            heroDescription: 'Your freelancer profile is not available yet.',
            stats: [
                ['label' => 'Applications', 'value' => '0', 'note' => 'Create your profile to start applying', 'id' => 'pendingSubmissions'],
                ['label' => 'Pending Reviews', 'value' => '0', 'note' => 'Waiting for a freelancer profile'],
                ['label' => 'Accepted', 'value' => '0', 'note' => 'No accepted submissions yet'],
                ['label' => 'Rejected', 'value' => '0', 'note' => 'No rejected submissions yet'],
            ],
            applications: [
                'title' => 'My Applications',
                'columns' => ['Gig Title', 'Category', 'Location', 'Status', 'Submitted At', 'Actions'],
                'rows' => [],
            ],
            recommendedGigs: [
                'title' => 'Recommended Gigs',
                'columns' => ['Title', 'Category', 'Location', 'Pay Rate', 'Actions'],
                'rows' => [],
            ],
            recentActions: [
                'title' => 'Recent Application Updates',
                'items' => [],
            ],
        )->toArray();
    }

    /**
     * Build summary stats for production dashboards.
     */
    private function buildProductionStats(array $gigs, array $submissions, string $role): array
    {
        $stats = $this->buildBaseProductionStats($gigs, $submissions, $role);

        if ($role === RoleType::ADMIN->value) {
            $stats[] = $this->buildProductionHousesStat();
        }

        return $stats;
    }

    /**
     * Build freelancer stats from submission totals.
     */
    private function buildFreelancerStats(array $submissions): array
    {
        $pendingCount = $this->countSubmissionsByStatus($submissions, SubmissionStatus::PENDING->value);
        $acceptedCount = $this->countSubmissionsByStatus($submissions, SubmissionStatus::ACCEPTED->value);
        $rejectedCount = $this->countSubmissionsByStatus($submissions, SubmissionStatus::REJECTED->value);

        return [
            ['label' => 'Applications', 'value' => (string) count($submissions), 'note' => 'All submissions', 'id' => 'pendingSubmissions'],
            ['label' => 'Pending Reviews', 'value' => (string) $pendingCount, 'note' => 'Waiting on production houses'],
            ['label' => 'Accepted', 'value' => (string) $acceptedCount, 'note' => 'Successful applications'],
            ['label' => 'Rejected', 'value' => (string) $rejectedCount, 'note' => 'Closed responses'],
        ];
    }

    /**
     * Build the freelancer applications section payload.
     */
    private function buildFreelancerApplicationsSection(array $submissions): array
    {
        return [
            'title' => 'My Applications',
            'columns' => ['Gig Title', 'Category', 'Location', 'Status', 'Submitted At', 'Actions'],
            'rows' => array_map(fn(Submission $submission): array => $this->mapFreelancerSubmissionToRow($submission), $submissions),
        ];
    }

    /**
     * Build the freelancer recommendations section payload.
     */
    private function buildFreelancerRecommendedSection(array $availableGigs): array
    {
        return [
            'title' => 'Recommended Gigs',
            'columns' => ['Title', 'Category', 'Location', 'Pay Rate', 'Actions'],
            'rows' => array_map(fn(Gig $gig): array => $this->mapRecommendedGigToRow($gig), array_slice($availableGigs, 0, 4)),
        ];
    }

    /**
     * Build the freelancer recent actions section payload.
     */
    private function buildFreelancerRecentActionsSection(array $submissions): array
    {
        return [
            'title' => 'Recent Application Updates',
            'items' => array_map(fn(Submission $submission): array => $this->mapSubmissionToListItem($submission, true), array_slice($submissions, 0, 5)),
        ];
    }

    /**
     * Build base stats shared by admin and production dashboards.
     */
    private function buildBaseProductionStats(array $gigs, array $submissions, string $role): array
    {
        return [
            [
                'label' => 'Active Gigs',
                'value' => (string) count(array_filter($gigs, static fn(Gig $gig): bool => $gig->getStatus() === 'active')),
                'note' => $role === RoleType::ADMIN->value ? 'Across the platform' : 'Your live gigs',
                'id' => 'activeGigs',
            ],
            [
                'label' => 'Pending Submissions',
                'value' => (string) $this->countSubmissionsByStatus($submissions, SubmissionStatus::PENDING->value),
                'note' => $role === RoleType::ADMIN->value ? 'Awaiting review' : 'Awaiting your review',
                'id' => 'pendingSubmissions',
            ],
            [
                'label' => 'Freelancers',
                'value' => (string) count($this->freelancerRepository->findAll()),
                'note' => 'Registered creators',
            ],
        ];
    }

    /**
     * Build the production-houses stat card used by admins.
     */
    private function buildProductionHousesStat(): array
    {
        return [
            'label' => 'Production Houses',
            'value' => (string) count($this->productionHouseRepository->findAll()),
            'note' => 'Publishing gigs',
        ];
    }

    /**
     * Build primary dashboard table rows for gigs.
     */
    private function buildProductionPrimaryRows(array $gigs, array $submissions, string $role): array
    {
        return array_map(function (Gig $gig) use ($submissions, $role): array {
            $applicantCount = count(array_filter(
                $submissions,
                static fn(Submission $submission): bool => $submission->getGigId() === $gig->getGigId()
            ));

            return [
                'title' => $gig->getTitle(),
                'status' => ucfirst($gig->getStatus()),
                'value' => (string) $applicantCount,
                'viewUrl' => '/gigs/' . $gig->getGigId(),
                'editUrl' => '/dashboard/gigs/' . $gig->getGigId(),
                'canEdit' => $role !== RoleType::ADMIN->value,
            ];
        }, array_slice($gigs, 0, 6));
    }

    /**
     * Build secondary dashboard list items from submissions.
     */
    private function buildProductionSecondaryItems(array $submissions): array
    {
        return array_map(
            fn(Submission $submission): array => $this->mapSubmissionToListItem($submission),
            array_slice($submissions, 0, 5)
        );
    }

    /**
     * Gather submissions for all provided gigs.
     */
    private function collectSubmissionsForGigs(array $gigs): array
    {
        $submissions = [];

        foreach ($gigs as $gig) {
            foreach ($this->submissionRepository->findByGigId($gig->getGigId()) as $submission) {
                $submissions[] = $submission;
            }
        }

        return $submissions;
    }

    /**
     * Resolve the hero description text for admin/production dashboards.
     */
    private function resolveProductionHeroDescription(string $role): string
    {
        if ($role === RoleType::ADMIN->value) {
            return 'Monitor live platform activity and oversee platform-wide gig performance.';
        }

        return 'Track your gigs, review live applications, and manage production activity from one place.';
    }

    /**
     * Return active gigs that the freelancer has not applied to.
     */
    private function findAvailableGigsForFreelancer(array $submissions): array
    {
        $appliedGigIds = array_map(static fn(Submission $submission): int => $submission->getGigId(), $submissions);

        $availableGigs = array_values(array_filter(
            $this->gigRepository->findAll(),
            static fn(Gig $gig): bool => $gig->getStatus() === 'active' && !in_array($gig->getGigId(), $appliedGigIds, true)
        ));

        usort($availableGigs, static fn(Gig $left, Gig $right): int => strcmp($right->getCreatedAt(), $left->getCreatedAt()));

        return $availableGigs;
    }

    /**
     * Count submissions by status value.
     */
    private function countSubmissionsByStatus(array $submissions, string $status): int
    {
        return count(array_filter(
            $submissions,
            static fn(Submission $submission): bool => $submission->getStatus() === $status
        ));
    }

    /**
     * Map a submission model to a reusable list item.
     */
    private function mapSubmissionToListItem(Submission $submission, bool $useGigTitleAsSubtitle = false): array
    {
        $gig = $this->gigRepository->findById($submission->getGigId());
        $freelancer = $this->freelancerRepository->findById($submission->getFreelancerId());
        $freelancerUser = $freelancer !== null ? $this->userRepository->findById($freelancer->getUserId()) : null;

        if ($useGigTitleAsSubtitle) {
            return [
                'title' => $gig !== null ? $gig->getTitle() : 'Unknown Gig',
                'subtitle' => 'Status: ' . ucfirst($submission->getStatus()),
                'meta' => $submission->getSubmittedAt(),
                'detailUrl' => $gig !== null ? '/gigs/' . $gig->getGigId() : '/gigs',
            ];
        }

        return [
            'title' => $freelancerUser !== null ? $freelancerUser->getName() : ($gig !== null ? $gig->getTitle() : 'Unknown Submission'),
            'subtitle' => ($gig !== null ? $gig->getTitle() : 'Unknown Gig') . ' · ' . ucfirst($submission->getStatus()),
            'meta' => $submission->getSubmittedAt(),
            'detailUrl' => $gig !== null ? '/gigs/' . $gig->getGigId() : '/gigs',
        ];
    }

    /**
     * Map one submission to a freelancer dashboard table row.
     */
    private function mapFreelancerSubmissionToRow(Submission $submission): array
    {
        $gig = $this->gigRepository->findById($submission->getGigId());

        return [
            'title' => $gig !== null ? $gig->getTitle() : 'Unknown Gig',
            'category' => $gig !== null ? $gig->getCategory() : '',
            'location' => $gig !== null ? $gig->getLocation() : '',
            'status' => ucfirst($submission->getStatus()),
            'submittedAt' => $submission->getSubmittedAt(),
            'detailUrl' => $gig !== null ? '/gigs/' . $gig->getGigId() : '/gigs',
            'canWithdraw' => $submission->getStatus() === SubmissionStatus::PENDING->value,
            'submissionId' => $submission->getSubmissionId(),
        ];
    }

    /**
     * Map one gig to a freelancer recommendation row.
     */
    private function mapRecommendedGigToRow(Gig $gig): array
    {
        return [
            'title' => $gig->getTitle(),
            'category' => $gig->getCategory(),
            'location' => $gig->getLocation(),
            'value' => sprintf('EUR %.2f', $gig->getPayRate()),
            'detailUrl' => '/gigs/' . $gig->getGigId(),
            'applyUrl' => '/gigs/' . $gig->getGigId(),
        ];
    }
}

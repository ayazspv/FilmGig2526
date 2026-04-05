<?php

namespace App\Controllers;

use App\Config;
use App\Enums\RoleType;
use App\Enums\SubmissionStatus;
use App\Framework\Controller;
use App\Models\Gig;
use App\Models\Submission;
use App\Repositories\FreelancerRepository;
use App\Repositories\GigRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\SubmissionRepository;
use App\Repositories\UserRepository;
use App\Services\GigService;
use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;
use App\ViewModels\FreelancerSubmissionsViewModel;
use App\ViewModels\AdminSubmissionReviewViewModel;
use App\ViewModels\AdminGigPostingViewModel;
use App\ViewModels\AdminGigEditingViewModel;
use App\ViewModels\AdminGigListingViewModel;
use App\Services\SubmissionService;
use App\Services\Interfaces\ISubmissionService;

/**
 * DashboardController handles dashboard routes and gig management.
 * 
 * Responsibilities:
 * - Route authentication and role-based authorization
 * - Display user dashboards by role
 * - Delegate gig business logic to GigService
 * - Handle form submissions and redirects
 * - Render view models with application data
 */
class DashboardController extends Controller
{
    /**
     * Route authenticated users to the correct dashboard by role.
     */
    public function showDashboard(array $params = []): void
    {
        $this->requireAuthentication();

        $role = $this->authUserRole();

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminDashboard($params);
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerDashboard($params);
            return;
        }

        $this->denyAndRedirectToSignin();
    }

    /**
     * Render the admin or production house dashboard.
     */
    public function showAdminDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $dashboardRole = $this->authUserRole() === RoleType::ADMIN->value ? RoleType::ADMIN->value : RoleType::PRODUCTION_HOUSE->value;
        $pageTitle = $dashboardRole === RoleType::ADMIN->value ? 'Admin Dashboard - FilmGig' : 'Production House Dashboard - FilmGig';
        $dashboardApiEndpoint = '/api/dashboard';
        $dashboardKind = $dashboardRole;

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    /**
     * Render the freelancer dashboard.
     */
    public function showFreelancerDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $pageTitle = 'Freelancer Dashboard - FilmGig';
        $dashboardApiEndpoint = '/api/dashboard';
        $dashboardKind = RoleType::FREELANCER->value;

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
    }

    /**
     * Return the authenticated dashboard data as JSON for client-side rendering.
     */
    public function apiDashboard(array $params = []): void
    {
        $this->requireAuthentication();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->buildDashboardPayload(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Render the freelancer submissions page.
     */
    public function showFreelancerSubmissions(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $submissions = $this->getSubmissionService()->getFreelancerSubmissions($this->getAuthenticatedOwnerId());

        $this->renderFreelancerSubmissions(
            $submissions,
            $this->consumeSubmissionFlashMessage('submission_success_message'),
            $this->consumeSubmissionFlashMessage('submission_error_message')
        );
    }

    /**
     * Render submissions received for gigs owned by the production house.
     */
    public function showProductionHouseSubmissions(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $submissions = $this->getSubmissionService()->getProductionHouseSubmissions($this->getAuthenticatedOwnerId());

        $this->renderProductionHouseSubmissions(
            $submissions,
            $this->consumeSubmissionFlashMessage('production_submission_review_success_message'),
            $this->consumeSubmissionFlashMessage('production_submission_review_error_message')
        );
    }

    /**
     * Handle accept or reject decisions for a submission.
     */
    public function handleProductionHouseSubmissionReview(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $submissionId = (int) ($params['id'] ?? 0);

        if ($submissionId <= 0) {
            $this->redirect('/dashboard/submissions/received');
        }

        $decision = (string) ($_POST['decision'] ?? '');
        $result = $this->getSubmissionService()->reviewSubmission($submissionId, $this->getAuthenticatedOwnerId(), $decision);
        $this->flashProductionHouseSubmissionReviewResult($result, 'Submission reviewed successfully.', 'Unable to review this submission right now.');

        $this->redirect('/dashboard/submissions/received');
    }

    /**
     * Withdraw a pending freelancer submission.
     */
    public function handleFreelancerSubmissionWithdrawal(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $submissionId = (int) ($params['id'] ?? 0);

        if ($submissionId <= 0) {
            $this->redirect('/dashboard/submissions');
        }

        $result = $this->getSubmissionService()->withdrawSubmission($submissionId, $this->getAuthenticatedOwnerId());
        $this->flashSubmissionResult($result, 'Your submission has been withdrawn.', 'Unable to withdraw your submission right now.');

        $this->redirect('/dashboard/submissions');
    }

    /**
     * Render production house gig listing screen.
     */
    public function showProductionHouseGigListing(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = $this->buildProductionHouseGigListingViewModel();

        include __DIR__ . '/../Views/dashboards/admin/gigListing.php';
    }

    /**
     * Render production house gig posting screen.
     */
    public function showProductionHouseGigPosting(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminGigPostingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    /**
     * Handle production house gig posting form submissions.
     * 
     * Delegates validation, image handling, and persistence to GigService.
     */
    public function handleProductionHouseGigPosting(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $ownerId = $this->getAuthenticatedOwnerId();
        $result = $this->getGigService()->create($ownerId, $_POST, $_FILES['image'] ?? []);

        $this->handleGigServiceResult($result, $params, 'post');
    }

    /**
     * Render production house gig editing screen.
     */
    public function showProductionHouseGigEditing(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigId = $this->extractAndValidateGigId($params);
        $gig = $this->findOwnedGigOrRedirect($gigId);

        if ($gig === null) {
            return;
        }

        $viewModel = AdminGigEditingViewModel::createWithGig($gig);

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
    }

    /**
     * Handle production house gig editing form submissions.
     * 
     * Delegates validation, image handling, and persistence to GigService.
     */
    public function handleProductionHouseGigEditing(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigId = $this->extractAndValidateGigId($params);
        $ownerId = $this->getAuthenticatedOwnerId();
        $existingGig = $this->findOwnedGigOrRedirect($gigId);

        if ($existingGig === null) {
            return;
        }

        $result = $this->getGigService()->update($gigId, $ownerId, $_POST, $_FILES['image'] ?? [], $existingGig->getImageUrl());

        $this->handleGigServiceResultWithGig($result, $existingGig, 'edit');
    }

    /**
     * Handle production house gig deletion requests.
     * 
     * Delegates ownership verification, deletion, and cleanup to GigService.
     */
    public function handleProductionHouseGigDeletion(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigId = $this->extractAndValidateGigId($params);
        $ownerId = $this->getAuthenticatedOwnerId();

        $this->getGigService()->delete($gigId, $ownerId);

        $this->redirectToGigListingWithSuccess('Gig deleted successfully.');
    }

    /**
     * Destroy session and send user back to signin.
     */
    private function denyAndRedirectToSignin(): void
    {
        $this->destroySession();
        $this->redirect('/signin');
    }

    /**
     * Render the production house gig posting form with old input and errors.
     * 
     * This is a helper for displaying the gig posting form with validation errors
     * and previously entered data for user correction.
     */
    private function renderProductionHouseGigPosting(array $oldInput = [], array $errors = []): void
    {
        $viewModel = AdminGigPostingViewModel::createWithInput($oldInput, $errors);

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    /**
     * Render the production house gig editing form with existing gig data.
     * 
     * This is a helper for displaying the gig editing form with current gig data,
     * validation errors, and the current image for user modification.
     */
    private function renderProductionHouseGigEditing(Gig $gig, array $oldInput = [], array $errors = []): void
    {
        $viewModel = AdminGigEditingViewModel::createWithGig($gig, $oldInput, $errors);

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
    }

    /**
     * Extract the authenticated owner ID from session.
     * 
     * @return int The owner ID
     */
    private function getAuthenticatedOwnerId(): int
    {
        return (int) ($_SESSION['auth_user_id'] ?? 0);
    }

    /**
     * Extract the authenticated owner name from session.
     * 
     * @return string The owner name
     */
    private function getAuthenticatedOwnerName(): string
    {
        return (string) ($_SESSION['auth_user_name'] ?? 'Production House');
    }

    /**
     * Build the dashboard payload for the current authenticated user.
     */
    private function buildDashboardPayload(): array
    {
        $role = $this->authUserRole();

        if ($role === RoleType::FREELANCER->value) {
            return $this->buildFreelancerDashboardPayload();
        }

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            return $this->buildProductionHouseDashboardPayload($role);
        }

        return [];
    }

    /**
     * Build the production house/admin dashboard payload.
     */
    private function buildProductionHouseDashboardPayload(string $role): array
    {
        $gigRepository = new GigRepository(Config::pdo());
        $submissionRepository = new SubmissionRepository(Config::pdo());
        $freelancerRepository = new FreelancerRepository(Config::pdo());
        $productionHouseRepository = new ProductionHouseRepository(Config::pdo());
        $userRepository = new UserRepository(Config::pdo());

        $ownerId = $this->getAuthenticatedOwnerId();
        $gigs = $role === RoleType::ADMIN->value ? $gigRepository->findAll() : $gigRepository->findByOwnerId($ownerId);
        $submissions = $role === RoleType::ADMIN->value ? $submissionRepository->findAll() : $this->collectSubmissionsForGigs($gigs, $submissionRepository);

        usort($gigs, static fn(Gig $left, Gig $right): int => strcmp($right->getCreatedAt(), $left->getCreatedAt()));
        usort($submissions, static fn(Submission $left, Submission $right): int => strcmp($right->getSubmittedAt(), $left->getSubmittedAt()));

        $badgeLabel = $role === RoleType::ADMIN->value ? 'Admin Dashboard' : 'Production House Dashboard';
        $heroDescription = $role === RoleType::ADMIN->value
            ? 'Monitor live platform activity and oversee platform-wide gig performance.'
            : 'Track your gigs, review live applications, and manage production activity from one place.';

        $stats = [
            [
                'label' => 'Active Gigs',
                'value' => (string) count(array_filter($gigs, static fn(Gig $gig): bool => $gig->getStatus() === 'active')),
                'note' => $role === RoleType::ADMIN->value ? 'Across the platform' : 'Your live gigs',
                'id' => 'activeGigs',
            ],
            [
                'label' => 'Pending Submissions',
                'value' => (string) count(array_filter($submissions, static fn(Submission $submission): bool => $submission->getStatus() === SubmissionStatus::PENDING->value)),
                'note' => $role === RoleType::ADMIN->value ? 'Awaiting review' : 'Awaiting your review',
                'id' => 'pendingSubmissions',
            ],
            [
                'label' => 'Freelancers',
                'value' => (string) count($freelancerRepository->findAll()),
                'note' => 'Registered creators',
            ],
        ];

        if ($role === RoleType::ADMIN->value) {
            $stats[] = [
                'label' => 'Production Houses',
                'value' => (string) count($productionHouseRepository->findAll()),
                'note' => 'Publishing gigs',
            ];
        }

        $primaryRows = array_map(function (Gig $gig) use ($submissions, $role): array {
            $applicantCount = count(array_filter($submissions, static fn(Submission $submission): bool => $submission->getGigId() === $gig->getGigId()));

            return [
                'title' => $gig->getTitle(),
                'status' => ucfirst($gig->getStatus()),
                'value' => (string) $applicantCount,
                'viewUrl' => '/gigs/' . $gig->getGigId(),
                'editUrl' => '/dashboard/gigs/' . $gig->getGigId(),
                'canEdit' => $role !== RoleType::ADMIN->value,
            ];
        }, array_slice($gigs, 0, 6));

        $secondaryItems = array_map(fn(Submission $submission): array => $this->mapSubmissionToDashboardListItem($submission, $userRepository), array_slice($submissions, 0, 5));

        return AdminDashboardViewModel::createFromData(
            pageTitle: $role === RoleType::ADMIN->value ? 'Admin Dashboard - FilmGig' : 'Production House Dashboard - FilmGig',
            badgeLabel: $badgeLabel,
            heroDescription: $heroDescription,
            stats: $stats,
            primaryTable: [
                'title' => 'Current Gigs Overview',
                'columns' => ['Gig Name', 'Status', 'Applicants', 'Actions'],
                'rows' => $primaryRows,
            ],
            secondaryList: [
                'title' => 'Recent Applications',
                'items' => $secondaryItems,
            ],
        )->toArray();
    }

    /**
     * Build the freelancer dashboard payload.
     */
    private function buildFreelancerDashboardPayload(): array
    {
        $gigRepository = new GigRepository(Config::pdo());
        $submissionRepository = new SubmissionRepository(Config::pdo());
        $freelancerRepository = new FreelancerRepository(Config::pdo());
        $userRepository = new UserRepository(Config::pdo());

        $userId = $this->getAuthenticatedOwnerId();
        $freelancer = $freelancerRepository->findByUserId($userId);

        if ($freelancer === null) {
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

        $submissions = $submissionRepository->findByFreelancerId($freelancer->getFreelancerId());
        usort($submissions, static fn(Submission $left, Submission $right): int => strcmp($right->getSubmittedAt(), $left->getSubmittedAt()));

        $appliedGigIds = array_map(static fn(Submission $submission): int => $submission->getGigId(), $submissions);
        $availableGigs = array_values(array_filter(
            $gigRepository->findAll(),
            static fn(Gig $gig): bool => $gig->getStatus() === 'active' && !in_array($gig->getGigId(), $appliedGigIds, true)
        ));

        usort($availableGigs, static fn(Gig $left, Gig $right): int => strcmp($right->getCreatedAt(), $left->getCreatedAt()));

        $submissionRows = array_map(fn(Submission $submission): array => $this->mapFreelancerSubmissionToDashboardRow($submission, $gigRepository), $submissions);
        $recommendedRows = array_map(fn(Gig $gig): array => $this->mapRecommendedGigToDashboardRow($gig), array_slice($availableGigs, 0, 4));
        $recentItems = array_map(fn(Submission $submission): array => $this->mapSubmissionToDashboardListItem($submission, $userRepository, true), array_slice($submissions, 0, 5));

        $pendingCount = count(array_filter($submissions, static fn(Submission $submission): bool => $submission->getStatus() === SubmissionStatus::PENDING->value));
        $acceptedCount = count(array_filter($submissions, static fn(Submission $submission): bool => $submission->getStatus() === SubmissionStatus::ACCEPTED->value));
        $rejectedCount = count(array_filter($submissions, static fn(Submission $submission): bool => $submission->getStatus() === SubmissionStatus::REJECTED->value));

        return FreelancerDashboardViewModel::createFromData(
            pageTitle: 'Freelancer Dashboard - FilmGig',
            badgeLabel: 'Freelancer Dashboard',
            heroDescription: 'Track opportunities, applications, and your ongoing projects.',
            stats: [
                ['label' => 'Applications', 'value' => (string) count($submissions), 'note' => 'All submissions', 'id' => 'pendingSubmissions'],
                ['label' => 'Pending Reviews', 'value' => (string) $pendingCount, 'note' => 'Waiting on production houses'],
                ['label' => 'Accepted', 'value' => (string) $acceptedCount, 'note' => 'Successful applications'],
                ['label' => 'Rejected', 'value' => (string) $rejectedCount, 'note' => 'Closed responses'],
            ],
            applications: [
                'title' => 'My Applications',
                'columns' => ['Gig Title', 'Category', 'Location', 'Status', 'Submitted At', 'Actions'],
                'rows' => $submissionRows,
            ],
            recommendedGigs: [
                'title' => 'Recommended Gigs',
                'columns' => ['Title', 'Category', 'Location', 'Pay Rate', 'Actions'],
                'rows' => $recommendedRows,
            ],
            recentActions: [
                'title' => 'Recent Application Updates',
                'items' => $recentItems,
            ],
        )->toArray();
    }

    /**
     * Collect submissions for the supplied gigs.
     */
    private function collectSubmissionsForGigs(array $gigs, SubmissionRepository $submissionRepository): array
    {
        $submissions = [];

        foreach ($gigs as $gig) {
            foreach ($submissionRepository->findByGigId($gig->getGigId()) as $submission) {
                $submissions[] = $submission;
            }
        }

        return $submissions;
    }

    /**
     * Map a submission to a dashboard list item.
     */
    private function mapSubmissionToDashboardListItem(Submission $submission, UserRepository $userRepository, bool $useGigTitleAsSubtitle = false): array
    {
        $gigRepository = new GigRepository(Config::pdo());
        $gig = $gigRepository->findById($submission->getGigId());
        $freelancerRepository = new FreelancerRepository(Config::pdo());
        $freelancer = $freelancerRepository->findById($submission->getFreelancerId());
        $freelancerUser = $freelancer !== null ? $userRepository->findById($freelancer->getUserId()) : null;

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
     * Map a submission to a freelancer dashboard row.
     */
    private function mapFreelancerSubmissionToDashboardRow(Submission $submission, GigRepository $gigRepository): array
    {
        $gig = $gigRepository->findById($submission->getGigId());

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
     * Map a gig to a recommended gig dashboard row.
     */
    private function mapRecommendedGigToDashboardRow(Gig $gig): array
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

    /**
     * Extract and validate the gig ID from route parameters.
     * 
     * Redirects to gig listing if the ID is invalid or missing.
     * 
     * @param array $params Route parameters
     * @return int The validated gig ID
     */
    private function extractAndValidateGigId(array $params): int
    {
        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            $this->redirect('/dashboard/gigs');
        }

        return $gigId;
    }

    /**
     * Handle gig service result for create/post operations.
     * 
     * On failure, re-renders the form with errors and old input.
     * On success, redirects to gig listing with success message.
     * 
     * @param array $result Service result array
     * @param array $params Route parameters (unused, for compatibility)
     * @param string $action The action type ('post' for create)
     * @return void
     */
    private function handleGigServiceResult(array $result, array $params = [], string $action = 'post'): void
    {
        if (($result['success'] ?? false) !== true) {
            $this->renderProductionHouseGigPosting(
                $result['input'] ?? [],
                $result['errors'] ?? ['general' => 'Unable to process gig.']
            );
            return;
        }

        $message = $action === 'post' ? 'Gig published successfully.' : 'Gig updated successfully.';
        $this->redirectToGigListingWithSuccess($message);
    }

    /**
     * Handle gig service result for update/edit operations.
     * 
     * On failure, re-renders the form with errors and old input along with existing gig.
     * On success, redirects to gig listing with success message.
     * 
     * @param array $result Service result array
     * @param Gig $gig The existing gig (for re-rendering on failure)
     * @param string $action The action type ('edit' for update)
     * @return void
     */
    private function handleGigServiceResultWithGig(array $result, Gig $gig, string $action = 'edit'): void
    {
        if (($result['success'] ?? false) !== true) {
            $this->renderProductionHouseGigEditing(
                $gig,
                $result['input'] ?? [],
                $result['errors'] ?? ['general' => 'Unable to process gig.']
            );
            return;
        }

        $message = $action === 'edit' ? 'Gig updated successfully.' : 'Gig changed successfully.';
        $this->redirectToGigListingWithSuccess($message);
    }

    /**
     * Redirect to gig listing with a success message stored in session.
     * 
     * @param string $message The success message to display
     * @return void
     */
    private function redirectToGigListingWithSuccess(string $message): void
    {
        $_SESSION['gig_success_message'] = $message;
        $this->redirect('/dashboard/gigs');
    }

    /**
     * Build a submission service instance.
     */
    private function getSubmissionService(): ISubmissionService
    {
        return new SubmissionService(Config::pdo());
    }

    /**
     * Build a gig service instance.
     */
    private function getGigService(): GigService
    {
        return new GigService(Config::pdo());
    }

    /**
     * Resolve a gig and ensure ownership; redirect when unavailable.
     */
    private function findOwnedGigOrRedirect(int $gigId): ?Gig
    {
        $gig = $this->getGigService()->findByIdAndVerifyOwnership($gigId, $this->getAuthenticatedOwnerId());

        if ($gig === null) {
            $this->redirect('/dashboard/gigs');
            return null;
        }

        return $gig;
    }

    /**
     * Build the production house listing view model.
     */
    private function buildProductionHouseGigListingViewModel(): AdminGigListingViewModel
    {
        $ownerId = $this->getAuthenticatedOwnerId();
        $ownerName = $this->getAuthenticatedOwnerName();
        $gigs = $this->getGigService()->findByOwnerId($ownerId);

        return AdminGigListingViewModel::createForGigs($gigs, $ownerName);
    }

    /**
     * Render the freelancer submissions page.
     */
    private function renderFreelancerSubmissions(array $submissions, ?string $successMessage = null, ?string $errorMessage = null): void
    {
        $viewModel = FreelancerSubmissionsViewModel::createFromSubmissions($submissions);

        include __DIR__ . '/../Views/dashboards/freelancer/submissions.php';
    }

    /**
     * Consume a one-time submission flash message.
     */
    private function consumeSubmissionFlashMessage(string $key): ?string
    {
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);

        return $message !== null ? (string) $message : null;
    }

    /**
     * Store the submission result message in session.
     */
    private function flashSubmissionResult(array $result, string $successFallback, string $errorFallback): void
    {
        if (($result['success'] ?? false) === true) {
            $_SESSION['submission_success_message'] = (string) ($result['message'] ?? $successFallback);
            return;
        }

        $_SESSION['submission_error_message'] = (string) (($result['errors']['general'] ?? $errorFallback));
    }

    /**
     * Render the production house submissions page.
     */
    private function renderProductionHouseSubmissions(array $submissions, ?string $successMessage = null, ?string $errorMessage = null): void
    {
        $viewModel = AdminSubmissionReviewViewModel::createForProductionHouseSubmissions($submissions);

        include __DIR__ . '/../Views/dashboards/admin/submissions.php';
    }

    /**
     * Store production house submission review feedback in session.
     */
    private function flashProductionHouseSubmissionReviewResult(array $result, string $successFallback, string $errorFallback): void
    {
        if (($result['success'] ?? false) === true) {
            $_SESSION['production_submission_review_success_message'] = (string) ($result['message'] ?? $successFallback);
            return;
        }

        $_SESSION['production_submission_review_error_message'] = (string) (($result['errors']['general'] ?? $errorFallback));
    }
}
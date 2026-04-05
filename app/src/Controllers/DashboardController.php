<?php

namespace App\Controllers;

use App\Config;
use App\Enums\RoleType;
use App\Framework\Controller;
use App\Models\Gig;
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

        $viewModel = AdminDashboardViewModel::createAdminDefault();

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    /**
     * Render the freelancer dashboard.
     */
    public function showFreelancerDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $viewModel = FreelancerDashboardViewModel::createFreelancerDefault();

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
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
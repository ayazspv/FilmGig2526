<?php

namespace App\Controllers;

use App\Config;
use App\ViewModels\GigListingViewModel;
use App\Framework\Controller;
use App\Services\GigReadService;
use App\Services\SubmissionService;
use App\Enums\RoleType;
use App\Repositories\GigRepository;

class GigController extends Controller
{
    /**
     * Render the public gig listing page.
     */
    public function showGigListing(array $params = []): void
    {
        $gigRepository = new GigRepository(Config::pdo());
        $viewModel = GigListingViewModel::createFromGigs($gigRepository->findAll());

        include __DIR__ . '/../Views/gigs/gigListing.php';
    }

    /**
     * Render the public detail page for a specific gig.
     */
    public function showGigDetail(array $params = []): void
    {
        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            $this->redirect('/gigs');
        }

        $detailData = $this->getGigReadService()->buildGigDetailData(
            $gigId,
            $this->authUserRole(),
            (int) ($_SESSION['auth_user_id'] ?? 0)
        );

        if ($detailData === null) {
            $this->redirect('/gigs');
        }

        $viewModel = $detailData['viewModel'];
        $hasAlreadyApplied = (bool) ($detailData['hasAlreadyApplied'] ?? false);
        $submissionSuccessMessage = $this->consumeFlashMessage('submission_success_message');
        $submissionErrorMessage = $this->consumeFlashMessage('submission_error_message');

        include __DIR__ . '/../Views/gigs/gigDetail.php';
    }

    /**
     * Handle a freelancer application for a gig.
     */
    public function handleGigApplication(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            $this->redirect('/gigs');
        }

        $service = new SubmissionService(Config::pdo());
        $result = $service->applyToGig($gigId, (int) ($_SESSION['auth_user_id'] ?? 0));

        if (($result['success'] ?? false) === true) {
            $_SESSION['submission_success_message'] = (string) ($result['message'] ?? 'Your application has been submitted successfully.');
        } else {
            $_SESSION['submission_error_message'] = (string) (($result['errors']['general'] ?? 'Unable to submit your application right now.') );
        }

        $this->redirect('/gigs/' . $gigId);
    }

    /**
     * Return all gigs as JSON for client-side rendering.
     */
    public function apiGigs(array $params = []): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $gigs = $this->getGigReadService()->getFilteredActiveGigs($_GET);
        echo json_encode(['gigs' => $gigs], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Build a one-time flash message and remove it from session.
     */
    private function consumeFlashMessage(string $key): ?string
    {
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);

        return $message !== null ? (string) $message : null;
    }

    /**
     * Build the gig read service for listing and detail payloads.
     */
    private function getGigReadService(): GigReadService
    {
        return new GigReadService(Config::pdo());
    }
}
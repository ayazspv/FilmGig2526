<?php

namespace App\Controllers;

use App\Config;
use App\Repositories\GigRepository;
use App\Repositories\FreelancerRepository;
use App\Repositories\ProductionHouseRepository;
use App\Repositories\UserRepository;
use App\ViewModels\GigListingViewModel;
use App\ViewModels\GigDetailViewModel;
use App\Framework\Controller;
use App\Services\SubmissionService;
use App\Enums\RoleType;

class GigController extends Controller
{
    public function showGigListing(array $params = []): void
    {
        $gigRepository = new GigRepository(Config::pdo());
        $viewModel = GigListingViewModel::createFromGigs($gigRepository->findAll());

        include __DIR__ . '/../Views/gigs/gigListing.php';
    }

    public function showGigDetail(array $params = []): void
    {
        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            header('Location: /gigs');
            exit;
        }

        $gigRepository = new GigRepository(Config::pdo());
        $userRepository = new UserRepository(Config::pdo());
        $productionHouseRepository = new ProductionHouseRepository(Config::pdo());

        $gig = $gigRepository->findById($gigId);

        if ($gig === null) {
            header('Location: /gigs');
            exit;
        }

        $owner = $userRepository->findById($gig->getOwnerId());
        $productionHouse = $productionHouseRepository->findByUserId($gig->getOwnerId());

        $viewModel = GigDetailViewModel::createFromGig($gig, $owner, $productionHouse);
        $hasAlreadyApplied = false;

        if (($this->authUserRole() ?? '') === RoleType::FREELANCER->value && (int) ($_SESSION['auth_user_id'] ?? 0) > 0) {
            $submissionService = new SubmissionService(Config::pdo());
            $freelancerSubmissions = $submissionService->getFreelancerSubmissions((int) $_SESSION['auth_user_id']);

            foreach ($freelancerSubmissions as $submission) {
                if ((int) ($submission['gigId'] ?? 0) === $gigId) {
                    $hasAlreadyApplied = true;
                    break;
                }
            }
        }

        $submissionSuccessMessage = $_SESSION['submission_success_message'] ?? null;
        $submissionErrorMessage = $_SESSION['submission_error_message'] ?? null;

        unset($_SESSION['submission_success_message'], $_SESSION['submission_error_message']);

        include __DIR__ . '/../Views/gigs/gigDetail.php';
    }

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
}
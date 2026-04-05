<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Framework\Service;
use App\Models\Submission;
use App\Repositories\FreelancerRepository;
use App\Repositories\GigRepository;
use App\Repositories\SubmissionRepository;
use App\Services\Interfaces\ISubmissionService;
use PDO;
use Throwable;

class SubmissionService extends Service implements ISubmissionService
{
    private GigRepository $gigRepository;
    private FreelancerRepository $freelancerRepository;
    private SubmissionRepository $submissionRepository;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->gigRepository = new GigRepository($pdo);
        $this->freelancerRepository = new FreelancerRepository($pdo);
        $this->submissionRepository = new SubmissionRepository($pdo);
    }

    public function applyToGig(int $gigId, int $userId): array
    {
        if (!$this->canProcessSubmission($gigId, $userId)) {
            return $this->buildFailureResult(['general' => 'Unable to submit your application right now.']);
        }

        $gig = $this->findActiveGig($gigId);

        if ($gig === null) {
            return $this->buildFailureResult(['general' => 'The selected gig could not be found.']);
        }

        if ($gig->getOwnerId() === $userId) {
            return $this->buildFailureResult(['general' => 'You cannot apply to your own gig.']);
        }

        $freelancer = $this->findFreelancerForUser($userId);

        if ($freelancer === null) {
            return $this->buildFailureResult(['general' => 'Your freelancer profile could not be found.']);
        }

        if ($this->hasExistingSubmission($gigId, $freelancer->getFreelancerId())) {
            return $this->buildFailureResult(['general' => 'You have already applied for this gig.']);
        }

        try {
            $submissionId = $this->createPendingSubmission($gigId, $freelancer->getFreelancerId());

            return $this->buildSuccessResult($submissionId);
        } catch (Throwable $exception) {
            return $this->buildFailureResult(['general' => 'Unable to submit your application right now. Please try again.']);
        }
    }

    public function getFreelancerSubmissions(int $userId): array
    {
        $freelancer = $this->findFreelancerForUser($userId);

        if ($freelancer === null) {
            return [];
        }

        $submissions = $this->submissionRepository->findByFreelancerId($freelancer->getFreelancerId());

        return array_map(fn(Submission $submission): array => $this->mapSubmissionToViewData($submission), $submissions);
    }

    public function withdrawSubmission(int $submissionId, int $userId): array
    {
        if ($submissionId <= 0 || $userId <= 0) {
            return $this->buildFailureResult(['general' => 'Unable to withdraw this submission right now.']);
        }

        $freelancer = $this->findFreelancerForUser($userId);

        if ($freelancer === null) {
            return $this->buildFailureResult(['general' => 'Your freelancer profile could not be found.']);
        }

        $submission = $this->findSubmissionForFreelancer($submissionId, $freelancer->getFreelancerId());

        if ($submission === null) {
            return $this->buildFailureResult(['general' => 'Submission could not be found.']);
        }

        if (!$this->canWithdrawSubmission($submission)) {
            return $this->buildFailureResult(['general' => 'Only pending submissions can be withdrawn.']);
        }

        try {
            $deleted = $this->deleteSubmission($submissionId);

            if (!$deleted) {
                return $this->buildFailureResult(['general' => 'Unable to withdraw this submission right now.']);
            }

            return [
                'success' => true,
                'message' => 'Your submission has been withdrawn.',
            ];
        } catch (Throwable $exception) {
            return $this->buildFailureResult(['general' => 'Unable to withdraw this submission right now. Please try again.']);
        }
    }

    private function buildSuccessResult(int $submissionId): array
    {
        return [
            'success' => true,
            'submissionId' => $submissionId,
            'message' => 'Your application has been submitted successfully.',
        ];
    }

    private function buildFailureResult(array $errors): array
    {
        return [
            'success' => false,
            'errors' => $errors,
        ];
    }

    /**
     * Check whether the submission creation flow can proceed.
     */
    private function canProcessSubmission(int $gigId, int $userId): bool
    {
        return $gigId > 0 && $userId > 0;
    }

    /**
     * Return the gig only if it exists and is active.
     */
    private function findActiveGig(int $gigId)
    {
        $gig = $this->gigRepository->findById($gigId);

        if ($gig === null || $gig->getStatus() !== 'active') {
            return null;
        }

        return $gig;
    }

    /**
     * Return the freelancer record for a user id.
     */
    private function findFreelancerForUser(int $userId): ?\App\Models\Freelancer
    {
        if ($userId <= 0) {
            return null;
        }

        return $this->freelancerRepository->findByUserId($userId);
    }

    /**
     * Determine whether a submission already exists for the freelancer and gig.
     */
    private function hasExistingSubmission(int $gigId, int $freelancerId): bool
    {
        return $this->submissionRepository->findByGigIdAndFreelancerId($gigId, $freelancerId) !== null;
    }

    /**
     * Create a pending submission inside a transaction.
     */
    private function createPendingSubmission(int $gigId, int $freelancerId): int
    {
        $this->pdo->beginTransaction();

        try {
            $submissionId = $this->submissionRepository->create([
                'gigId' => $gigId,
                'freelancerId' => $freelancerId,
                'status' => SubmissionStatus::PENDING->value,
            ]);

            $this->pdo->commit();

            return $submissionId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Convert a submission model into view data.
     */
    private function mapSubmissionToViewData(Submission $submission): array
    {
        $gig = $this->gigRepository->findById($submission->getGigId());

        return [
            'submissionId' => $submission->getSubmissionId(),
            'gigId' => $submission->getGigId(),
            'gigTitle' => $gig !== null ? $gig->getTitle() : 'Unknown Gig',
            'gigCategory' => $gig !== null ? $gig->getCategory() : '',
            'gigLocation' => $gig !== null ? $gig->getLocation() : '',
            'status' => $submission->getStatus(),
            'submittedAt' => $submission->getSubmittedAt(),
            'canWithdraw' => $this->canWithdrawSubmission($submission),
            'detailUrl' => $gig !== null ? '/gigs/' . $gig->getGigId() : '/gigs',
        ];
    }

    /**
     * Find a submission that belongs to a freelancer.
     */
    private function findSubmissionForFreelancer(int $submissionId, int $freelancerId): ?Submission
    {
        $submission = $this->submissionRepository->findById($submissionId);

        if ($submission === null || $submission->getFreelancerId() !== $freelancerId) {
            return null;
        }

        return $submission;
    }

    /**
     * Decide whether a submission can still be withdrawn.
     */
    private function canWithdrawSubmission(Submission $submission): bool
    {
        return $submission->getStatus() === SubmissionStatus::PENDING->value;
    }

    /**
     * Delete a submission row.
     */
    private function deleteSubmission(int $submissionId): bool
    {
        $this->pdo->beginTransaction();

        try {
            $deleted = $this->submissionRepository->delete($submissionId);

            if (!$deleted) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }

                return false;
            }

            $this->pdo->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }
}
<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Framework\Service;
use App\Models\Freelancer;
use App\Models\Submission;
use App\Repositories\FreelancerRepository;
use App\Repositories\GigRepository;
use App\Repositories\UserRepository;
use App\Repositories\SubmissionRepository;
use App\Services\Interfaces\ISubmissionService;
use PDO;
use Throwable;

class SubmissionService extends Service implements ISubmissionService
{
    private GigRepository $gigRepository;
    private FreelancerRepository $freelancerRepository;
    private UserRepository $userRepository;
    private SubmissionRepository $submissionRepository;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->gigRepository = new GigRepository($pdo);
        $this->freelancerRepository = new FreelancerRepository($pdo);
        $this->userRepository = new UserRepository($pdo);
        $this->submissionRepository = new SubmissionRepository($pdo);
    }

    public function applyToGig(int $gigId, int $userId): array
    {
        if ($failure = $this->validateApplyRequest($gigId, $userId)) {
            return $failure;
        }

        $gig = $this->resolveApplyGig($gigId, $userId);

        if (($gig['failure'] ?? null) !== null) {
            return $gig['failure'];
        }

        $freelancer = $this->resolveApplyFreelancer($gigId, $userId);

        if (($freelancer['failure'] ?? null) !== null) {
            return $freelancer['failure'];
        }

        return $this->executeApply((int) $gigId, (int) $freelancer['freelancerId']);
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
        if ($failure = $this->validateWithdrawRequest($submissionId, $userId)) {
            return $failure;
        }

        $freelancer = $this->resolveWithdrawalFreelancer($userId);

        if (($freelancer['failure'] ?? null) !== null) {
            return $freelancer['failure'];
        }

        $submission = $this->resolveWithdrawalSubmission($submissionId, (int) $freelancer['freelancerId']);

        if (($submission['failure'] ?? null) !== null) {
            return $submission['failure'];
        }

        return $this->executeWithdraw($submissionId);
    }

    public function getProductionHouseSubmissions(int $ownerUserId): array
    {
        if ($ownerUserId <= 0) {
            return [];
        }

        $ownedSubmissions = $this->collectOwnedGigSubmissions($ownerUserId);
        $this->sortSubmissionsByMostRecent($ownedSubmissions);

        return $this->mapReviewSubmissionsToViewData($ownedSubmissions);
    }

    public function reviewSubmission(int $submissionId, int $ownerUserId, string $decision): array
    {
        $decisionStatus = $this->parseDecisionStatus($decision);

        if ($failure = $this->validateReviewRequest($submissionId, $ownerUserId, $decisionStatus)) {
            return $failure;
        }

        $submission = $this->resolveReviewSubmission($submissionId);

        if (($submission['failure'] ?? null) !== null) {
            return $submission['failure'];
        }

        $ownership = $this->validateReviewOwnership($submission['submission'], $ownerUserId);

        if (($ownership['failure'] ?? null) !== null) {
            return $ownership['failure'];
        }

        return $this->executeReview($submission['submission'], $decisionStatus);
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
     * Validate input values for applying to a gig.
     */
    private function validateApplyRequest(int $gigId, int $userId): ?array
    {
        if ($this->canProcessSubmission($gigId, $userId)) {
            return null;
        }

        return $this->buildFailureResult(['general' => 'Unable to submit your application right now.']);
    }

    /**
     * Resolve and validate the gig targeted for an application.
     */
    private function resolveApplyGig(int $gigId, int $userId): array
    {
        $gig = $this->findActiveGig($gigId);

        if ($gig === null) {
            return ['gig' => null, 'failure' => $this->buildFailureResult(['general' => 'The selected gig could not be found.'])];
        }

        if ($gig->getOwnerId() === $userId) {
            return ['gig' => null, 'failure' => $this->buildFailureResult(['general' => 'You cannot apply to your own gig.'])];
        }

        return ['gig' => $gig, 'failure' => null];
    }

    /**
     * Resolve and validate the freelancer for a gig application.
     */
    private function resolveApplyFreelancer(int $gigId, int $userId): array
    {
        $freelancer = $this->findFreelancerForUser($userId);

        if ($freelancer === null) {
            return ['freelancerId' => null, 'failure' => $this->buildFailureResult(['general' => 'Your freelancer profile could not be found.'])];
        }

        if ($this->hasExistingSubmission($gigId, $freelancer->getFreelancerId())) {
            return ['freelancerId' => null, 'failure' => $this->buildFailureResult(['general' => 'You have already applied for this gig.'])];
        }

        return ['freelancerId' => $freelancer->getFreelancerId(), 'failure' => null];
    }

    /**
     * Persist a new pending submission for a freelancer application.
     */
    private function executeApply(int $gigId, int $freelancerId): array
    {
        try {
            $submissionId = $this->createPendingSubmission($gigId, $freelancerId);

            return $this->buildSuccessResult($submissionId);
        } catch (Throwable $exception) {
            return $this->buildFailureResult(['general' => 'Unable to submit your application right now. Please try again.']);
        }
    }

    /**
     * Validate input values for withdrawing a submission.
     */
    private function validateWithdrawRequest(int $submissionId, int $userId): ?array
    {
        if ($submissionId > 0 && $userId > 0) {
            return null;
        }

        return $this->buildFailureResult(['general' => 'Unable to withdraw this submission right now.']);
    }

    /**
     * Resolve and validate the freelancer requesting a withdrawal.
     */
    private function resolveWithdrawalFreelancer(int $userId): array
    {
        $freelancer = $this->findFreelancerForUser($userId);

        if ($freelancer === null) {
            return ['freelancerId' => null, 'failure' => $this->buildFailureResult(['general' => 'Your freelancer profile could not be found.'])];
        }

        return ['freelancerId' => $freelancer->getFreelancerId(), 'failure' => null];
    }

    /**
     * Resolve and validate a submission targeted for withdrawal.
     */
    private function resolveWithdrawalSubmission(int $submissionId, int $freelancerId): array
    {
        $submission = $this->findSubmissionForFreelancer($submissionId, $freelancerId);

        if ($submission === null) {
            return ['submission' => null, 'failure' => $this->buildFailureResult(['general' => 'Submission could not be found.'])];
        }

        if (!$this->canWithdrawSubmission($submission)) {
            return ['submission' => null, 'failure' => $this->buildFailureResult(['general' => 'Only pending submissions can be withdrawn.'])];
        }

        return ['submission' => $submission, 'failure' => null];
    }

    /**
     * Delete a pending submission and return a response payload.
     */
    private function executeWithdraw(int $submissionId): array
    {
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

    /**
     * Collect all submissions for gigs owned by one production house.
     */
    private function collectOwnedGigSubmissions(int $ownerUserId): array
    {
        $ownedGigs = $this->gigRepository->findByOwnerId($ownerUserId);
        $ownedSubmissions = [];

        foreach ($ownedGigs as $gig) {
            foreach ($this->submissionRepository->findByGigId($gig->getGigId()) as $submission) {
                $ownedSubmissions[] = $submission;
            }
        }

        return $ownedSubmissions;
    }

    /**
     * Sort submission rows descending by submitted timestamp.
     */
    private function sortSubmissionsByMostRecent(array &$submissions): void
    {
        usort(
            $submissions,
            static fn(Submission $left, Submission $right): int => strcmp($right->getSubmittedAt(), $left->getSubmittedAt())
        );
    }

    /**
     * Map submission models for review list rendering.
     */
    private function mapReviewSubmissionsToViewData(array $submissions): array
    {
        return array_map(fn(Submission $submission): array => $this->mapReviewSubmissionToViewData($submission), $submissions);
    }

    /**
     * Parse decision string into enum status.
     */
    private function parseDecisionStatus(string $decision): ?SubmissionStatus
    {
        return SubmissionStatus::tryFrom(strtolower(trim($decision)));
    }

    /**
     * Validate input values for reviewing a submission.
     */
    private function validateReviewRequest(int $submissionId, int $ownerUserId, ?SubmissionStatus $decisionStatus): ?array
    {
        if ($submissionId > 0 && $ownerUserId > 0 && $this->isReviewDecisionValid($decisionStatus)) {
            return null;
        }

        return $this->buildFailureResult(['general' => 'Unable to review this submission right now.']);
    }

    /**
     * Resolve and validate a pending submission for review.
     */
    private function resolveReviewSubmission(int $submissionId): array
    {
        $submission = $this->submissionRepository->findById($submissionId);

        if ($submission === null) {
            return ['submission' => null, 'failure' => $this->buildFailureResult(['general' => 'Submission could not be found.'])];
        }

        if ($submission->getStatus() !== SubmissionStatus::PENDING->value) {
            return ['submission' => null, 'failure' => $this->buildFailureResult(['general' => 'Only pending submissions can be reviewed.'])];
        }

        return ['submission' => $submission, 'failure' => null];
    }

    /**
     * Validate whether the current owner is allowed to review a submission.
     */
    private function validateReviewOwnership(Submission $submission, int $ownerUserId): array
    {
        $gig = $this->gigRepository->findById($submission->getGigId());

        if ($gig === null) {
            return ['failure' => $this->buildFailureResult(['general' => 'The related gig could not be found.'])];
        }

        if ($gig->getOwnerId() !== $ownerUserId) {
            return ['failure' => $this->buildFailureResult(['general' => 'You can only review submissions for your own gigs.'])];
        }

        return ['failure' => null];
    }

    /**
     * Persist a review decision for one submission.
     */
    private function executeReview(Submission $submission, SubmissionStatus $decisionStatus): array
    {
        try {
            $this->pdo->beginTransaction();
            $this->updateSubmissionStatus($submission, $decisionStatus);
            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Submission reviewed successfully.',
            ];
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return $this->buildFailureResult(['general' => 'Unable to review this submission right now. Please try again.']);
        }
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
     * Determine whether the admin decision is valid.
     */
    private function isReviewDecisionValid(?SubmissionStatus $decisionStatus): bool
    {
        return $decisionStatus === SubmissionStatus::ACCEPTED || $decisionStatus === SubmissionStatus::REJECTED;
    }

    /**
     * Convert a submission into review table data.
     */
    private function mapReviewSubmissionToViewData(Submission $submission): array
    {
        $gig = $this->gigRepository->findById($submission->getGigId());
        $freelancer = $this->freelancerRepository->findById($submission->getFreelancerId());
        $freelancerUser = $freelancer !== null ? $this->userRepository->findById($freelancer->getUserId()) : null;

        return [
            'submissionId' => $submission->getSubmissionId(),
            'gigId' => $submission->getGigId(),
            'gigTitle' => $gig !== null ? $gig->getTitle() : 'Unknown Gig',
            'gigStatus' => $gig !== null ? $gig->getStatus() : 'unknown',
            'gigStatusLabel' => $gig !== null ? ucfirst($gig->getStatus()) : 'Unknown',
            'freelancerName' => $freelancerUser !== null ? $freelancerUser->getName() : 'Unknown Freelancer',
            'freelancerEmail' => $freelancerUser !== null ? $freelancerUser->getEmail() : '',
            'status' => $submission->getStatus(),
            'statusLabel' => ucfirst($submission->getStatus()),
            'submittedAt' => $submission->getSubmittedAt(),
            'canReview' => $submission->getStatus() === SubmissionStatus::PENDING->value,
            'detailUrl' => $gig !== null ? '/gigs/' . $gig->getGigId() : '/gigs',
        ];
    }

    /**
     * Update a submission status in the database.
     */
    private function updateSubmissionStatus(Submission $submission, SubmissionStatus $status): bool
    {
        return $this->submissionRepository->update($submission->getSubmissionId(), [
            'gigId' => $submission->getGigId(),
            'freelancerId' => $submission->getFreelancerId(),
            'status' => $status->value,
        ]);
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
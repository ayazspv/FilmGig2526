<?php

namespace App\Services\Interfaces;

interface ISubmissionService
{
    /**
     * Create a submission for the authenticated freelancer.
     */
    public function applyToGig(int $gigId, int $userId): array;

    /**
     * Return submissions for the authenticated freelancer.
     */
    public function getFreelancerSubmissions(int $userId): array;

    /**
     * Withdraw a pending submission.
     */
    public function withdrawSubmission(int $submissionId, int $userId): array;
}
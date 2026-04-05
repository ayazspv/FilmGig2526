<?php

namespace App\Models;

class Submission
{
    private int $submissionId;      // Primary Key (int)
    private int $gigId;             // Foreign Key → Gigs (int)
    private int $freelancerId;      // Foreign Key → Freelancers (int)
    private string $status;         // ENUM('pending', 'accepted', 'rejected')
    private string $submittedAt;    // TIMESTAMP (string format: YYYY-MM-DD HH:MM:SS)

    public function __construct(
        int $submissionId,
        int $gigId,
        int $freelancerId,
        string $status,
        string $submittedAt
    ) {
        $this->submissionId = $submissionId;
        $this->gigId = $gigId;
        $this->freelancerId = $freelancerId;
        $this->status = $status;
        $this->submittedAt = $submittedAt;
    }

    // Getters
    public function getSubmissionId(): int
    {
        return $this->submissionId;
    }

    public function getGigId(): int
    {
        return $this->gigId;
    }

    public function getFreelancerId(): int
    {
        return $this->freelancerId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubmittedAt(): string
    {
        return $this->submittedAt;
    }

    // Setters
    public function setSubmissionId(int $submissionId): void
    {
        $this->submissionId = $submissionId;
    }

    public function setGigId(int $gigId): void
    {
        $this->gigId = $gigId;
    }

    public function setFreelancerId(int $freelancerId): void
    {
        $this->freelancerId = $freelancerId;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setSubmittedAt(string $submittedAt): void
    {
        $this->submittedAt = $submittedAt;
    }
}
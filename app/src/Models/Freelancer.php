<?php

namespace App\Models;

class Freelancer
{
    private int $freelancerId;       // Integer
    private int $userId;           // Integer
    private string $dateOfBirth;     // String (YYYY-MM-DD)
    private string $createdAt;     // String (DateTime in string format)

    public function __construct(
        int $freelancerId,
        int $userId,
        string $dateOfBirth,
        string $createdAt
    ) {
        $this->freelancerId = $freelancerId;
        $this->userId = $userId;
        $this->dateOfBirth = $dateOfBirth;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getFreelancerId(): int
    {
        return $this->freelancerId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getDateOfBirth(): string
    {
        return $this->dateOfBirth;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    // Setters
    public function setFreelancerId(int $freelancerId): void
    {
        $this->freelancerId = $freelancerId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setDateOfBirth(string $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
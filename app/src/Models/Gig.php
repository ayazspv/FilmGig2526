<?php

namespace App\Models;

class Gig
{
    private int $gigId;            // Integer
    private int $ownerId;          // Foreign Key → Users (int)
    private string $imageUrl;
    private string $title;
    private string $description;
    private string $category;
    private string $location;
    private string $startDate;     // String (YYYY-MM-DD)
    private string $rateType;      // String (enum)
    private float $payRate;        // Decimal (10,2)
    private string $status;        // String (enum)
    private string $createdAt;     // String (timestamp)

    // Getters
    public function getGigId(): int
    {
        return $this->gigId;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getRateType(): string
    {
        return $this->rateType;
    }

    public function getPayRate(): float
    {
        return $this->payRate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    // Setters
    public function setGigId(int $gigId): void
    {
        $this->gigId = $gigId;
    }

    public function setOwnerId(int $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function setRateType(string $rateType): void
    {
        $this->rateType = $rateType;
    }

    public function setPayRate(float $payRate): void
    {
        $this->payRate = $payRate;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
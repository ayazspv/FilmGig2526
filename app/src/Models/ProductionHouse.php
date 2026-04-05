<?php

namespace App\Models;

class ProductionHouse
{
    private int $productionHouseId; // Primary Key (int)
    private int $userId;            // Foreign Key → Users (int)
    private string $companyName;    // VARCHAR(255)
    private string $website;        // VARCHAR(255)
    private string $createdAt;      // TIMESTAMP

    public function __construct(
        int $productionHouseId,
        int $userId,
        string $companyName,
        string $website,
        string $createdAt
    ) {
        $this->productionHouseId = $productionHouseId;
        $this->userId = $userId;
        $this->companyName = $companyName;
        $this->website = $website;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getProductionHouseId(): int
    {
        return $this->productionHouseId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    // Setters
    public function setProductionHouseId(int $productionHouseId): void
    {
        $this->productionHouseId = $productionHouseId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
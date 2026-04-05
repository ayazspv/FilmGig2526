<?php

namespace App\Models;

class User
{
    private int $userId;         // Primary Key (int)
    private string $username;
    private string $name;        // VARCHAR(255)
    private string $email;       // VARCHAR(255)
    private string $password;    // VARCHAR(255)
    private string $role;        // ENUM('admin', 'productionHouse', 'freelancer')
    private string $address;
    private string $bio;           // String
    private int $kvkNr;          // INT(8)
    private string $createdAt;   // TIMESTAMP (string format: YYYY-MM-DD HH:MM:SS)

    public function __construct(
        int $userId,
        string $username,
        string $name,
        string $email,
        string $password,
        string $role,
        string $address,
        string $bio,
        int $kvkNr,
        string $createdAt
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->address = $address;
        $this->bio = $bio;
        $this->kvkNr = $kvkNr;
        $this->createdAt = $createdAt;
    }

    // Getters
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getKvkNr(): int
    {
        return $this->kvkNr;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    // Setters
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setBio(string $bio): void
    {
        $this->bio = $bio;
    }

    public function setKvkNr(int $kvkNr): void
    {
        $this->kvkNr = $kvkNr;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}
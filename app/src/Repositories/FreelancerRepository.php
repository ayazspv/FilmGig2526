<?php

namespace App\Repositories;

use PDO;

class FreelancerRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO freelancer (userId, dateOfBirth)
             VALUES (:userId, :dateOfBirth)'
        );

        $statement->execute([
            'userId' => $userId,
            'dateOfBirth' => $data['dateOfBirth'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}

<?php

namespace App\Repositories;

use PDO;

class ProductionHouseRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(int $userId, array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO productionHouse (userId, companyName, website)
             VALUES (:userId, :companyName, :website)'
        );

        $statement->execute([
            'userId' => $userId,
            'companyName' => $data['companyName'],
            'website' => $data['website'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}

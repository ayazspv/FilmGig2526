<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Freelancer;
use App\Repositories\Interfaces\IFreelancerRepository;

class FreelancerRepository extends Repository implements IFreelancerRepository
{
    protected function tableName(): string
    {
        return 'freelancer';
    }

    protected function primaryKey(): string
    {
        return 'freelancerId';
    }

    public function findById(int $freelancerId): ?Freelancer
    {
        $row = $this->findRowById($freelancerId);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

    public function findAll(): array
    {
        $rows = $this->findAllRowsFromTable();

        return array_map(fn(array $row): Freelancer => $this->mapRowToModel($row), $rows);
    }

    public function findByUserId(int $userId): ?Freelancer
    {
        $row = $this->fetchOneRow('SELECT * FROM freelancer WHERE userId = :userId LIMIT 1', ['userId' => $userId]);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

    public function create(int $userId, array $data): int
    {
        return $this->insertAndReturnId(
            'INSERT INTO freelancer (userId, dateOfBirth)
             VALUES (:userId, :dateOfBirth)',
            [
                'userId' => $userId,
                'dateOfBirth' => $data['dateOfBirth'] ?? null,
            ]
        );
    }

    public function update(int $freelancerId, array $data): bool
    {
        return $this->executeStatement(
            'UPDATE freelancer
             SET userId = :userId,
                 dateOfBirth = :dateOfBirth
             WHERE freelancerId = :freelancerId',
            [
                'freelancerId' => $freelancerId,
                'userId' => $data['userId'],
                'dateOfBirth' => $data['dateOfBirth'] ?? null,
            ]
        );
    }

    public function delete(int $freelancerId): bool
    {
        return $this->deleteRowById($freelancerId);
    }

    private function mapRowToModel(array $row): Freelancer
    {
        return new Freelancer(
            (int) $row['freelancerId'],
            (int) $row['userId'],
            (string) ($row['dateOfBirth'] ?? ''),
            (string) $row['createdAt'],
        );
    }
}

<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\ProductionHouse;
use App\Repositories\Interfaces\IProductionHouseRepository;

class ProductionHouseRepository extends Repository implements IProductionHouseRepository
{
    protected function tableName(): string
    {
        return 'productionHouse';
    }

    protected function primaryKey(): string
    {
        return 'productionHouseId';
    }

    public function findById(int $productionHouseId): ?ProductionHouse
    {
        $row = $this->findRowById($productionHouseId);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

    public function findAll(): array
    {
        $rows = $this->findAllRowsFromTable();

        return array_map(fn(array $row): ProductionHouse => $this->mapRowToModel($row), $rows);
    }

    public function findByUserId(int $userId): ?ProductionHouse
    {
        $row = $this->fetchOneRow('SELECT * FROM productionHouse WHERE userId = :userId LIMIT 1', ['userId' => $userId]);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

    public function create(int $userId, array $data): int
    {
        return $this->insertAndReturnId(
            'INSERT INTO productionHouse (userId, companyName, website)
             VALUES (:userId, :companyName, :website)',
            [
                'userId' => $userId,
                'companyName' => $data['companyName'] ?? '',
                'website' => $data['website'] ?? '',
            ]
        );
    }

    public function update(int $productionHouseId, array $data): bool
    {
        return $this->executeStatement(
            'UPDATE productionHouse
             SET userId = :userId,
                 companyName = :companyName,
                 website = :website
             WHERE productionHouseId = :productionHouseId',
            [
                'productionHouseId' => $productionHouseId,
                'userId' => $data['userId'],
                'companyName' => $data['companyName'] ?? '',
                'website' => $data['website'] ?? '',
            ]
        );
    }

    public function delete(int $productionHouseId): bool
    {
        return $this->deleteRowById($productionHouseId);
    }

    private function mapRowToModel(array $row): ProductionHouse
    {
        return new ProductionHouse(
            (int) $row['productionHouseId'],
            (int) $row['userId'],
            (string) ($row['companyName'] ?? ''),
            (string) ($row['website'] ?? ''),
            (string) $row['createdAt'],
        );
    }
}

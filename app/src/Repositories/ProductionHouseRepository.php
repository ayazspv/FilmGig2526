<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\ProductionHouse;
use App\Repositories\Interfaces\IProductionHouseRepository;

class ProductionHouseRepository extends Repository implements IProductionHouseRepository
{
	/**
	 * Return the backing production house table name.
	 */
    protected function tableName(): string
    {
        return 'productionHouse';
    }

	/**
	 * Return the primary key column for production houses.
	 */
    protected function primaryKey(): string
    {
        return 'productionHouseId';
    }

	/**
	 * Find a production house by id.
	 */
    public function findById(int $productionHouseId): ?ProductionHouse
    {
        $row = $this->findRowById($productionHouseId);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

	/**
	 * Return all production house records.
	 */
    public function findAll(): array
    {
        $rows = $this->findAllRowsFromTable();

        return array_map(fn(array $row): ProductionHouse => $this->mapRowToModel($row), $rows);
    }

	/**
	 * Find a production house by its linked user id.
	 */
    public function findByUserId(int $userId): ?ProductionHouse
    {
        $row = $this->fetchOneRow('SELECT * FROM productionHouse WHERE userId = :userId LIMIT 1', ['userId' => $userId]);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

	/**
	 * Create a production house record for a user.
	 */
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

	/**
	 * Update a production house record.
	 */
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

	/**
	 * Delete a production house record.
	 */
    public function delete(int $productionHouseId): bool
    {
        return $this->deleteRowById($productionHouseId);
    }

	/**
	 * Map a database row to a ProductionHouse model.
	 */
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

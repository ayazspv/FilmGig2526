<?php

namespace App\Repositories\Interfaces;

use App\Models\ProductionHouse;

interface IProductionHouseRepository
{
	/**
	 * Find a production house by primary key.
	 */
	public function findById(int $productionHouseId): ?ProductionHouse;

	/**
	 * Return all production house records.
	 */
	public function findAll(): array;

	/**
	 * Find a production house by user id.
	 */
	public function findByUserId(int $userId): ?ProductionHouse;

	/**
	 * Create a production house record for a user.
	 */
	public function create(int $userId, array $data): int;

	/**
	 * Update a production house record.
	 */
	public function update(int $productionHouseId, array $data): bool;

	/**
	 * Delete a production house record.
	 */
	public function delete(int $productionHouseId): bool;
}

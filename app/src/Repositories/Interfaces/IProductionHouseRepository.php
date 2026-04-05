<?php

namespace App\Repositories\Interfaces;

use App\Models\ProductionHouse;

interface IProductionHouseRepository
{
	public function findById(int $productionHouseId): ?ProductionHouse;

	/** @return ProductionHouse[] */
	public function findAll(): array;

	public function findByUserId(int $userId): ?ProductionHouse;

	public function create(int $userId, array $data): int;

	public function update(int $productionHouseId, array $data): bool;

	public function delete(int $productionHouseId): bool;
}

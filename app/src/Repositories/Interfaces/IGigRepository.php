<?php

namespace App\Repositories\Interfaces;

use App\Models\Gig;

interface IGigRepository
{
	public function findById(int $gigId): ?Gig;

	/** @return Gig[] */
	public function findAll(): array;

	/** @return Gig[] */
	public function findByOwnerId(int $ownerId): array;

	public function create(array $data): int;

	public function update(int $gigId, array $data): bool;

	public function delete(int $gigId): bool;
}

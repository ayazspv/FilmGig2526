<?php

namespace App\Repositories\Interfaces;

use App\Models\Gig;

interface IGigRepository
{
	/**
	 * Find a gig by primary key.
	 */
	public function findById(int $gigId): ?Gig;

	/**
	 * Return all gigs.
	 */
	public function findAll(): array;

	/**
	 * Find gigs owned by a specific user.
	 */
	public function findByOwnerId(int $ownerId): array;

	/**
	 * Create a gig record.
	 */
	public function create(array $data): int;

	/**
	 * Update a gig record.
	 */
	public function update(int $gigId, array $data): bool;

	/**
	 * Delete a gig record.
	 */
	public function delete(int $gigId): bool;
}

<?php

namespace App\Repositories\Interfaces;

use App\Models\Freelancer;

interface IFreelancerRepository
{
	/**
	 * Find a freelancer by primary key.
	 */
	public function findById(int $freelancerId): ?Freelancer;

	/**
	 * Return all freelancer records.
	 */
	public function findAll(): array;

	/**
	 * Find a freelancer by user id.
	 */
	public function findByUserId(int $userId): ?Freelancer;

	/**
	 * Create a freelancer record for a user.
	 */
	public function create(int $userId, array $data): int;

	/**
	 * Update a freelancer record.
	 */
	public function update(int $freelancerId, array $data): bool;

	/**
	 * Delete a freelancer record.
	 */
	public function delete(int $freelancerId): bool;
}

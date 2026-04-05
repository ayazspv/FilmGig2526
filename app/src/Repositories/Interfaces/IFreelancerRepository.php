<?php

namespace App\Repositories\Interfaces;

use App\Models\Freelancer;

interface IFreelancerRepository
{
	public function findById(int $freelancerId): ?Freelancer;

	/** @return Freelancer[] */
	public function findAll(): array;

	public function findByUserId(int $userId): ?Freelancer;

	public function create(int $userId, array $data): int;

	public function update(int $freelancerId, array $data): bool;

	public function delete(int $freelancerId): bool;
}

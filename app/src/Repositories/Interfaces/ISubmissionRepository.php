<?php

namespace App\Repositories\Interfaces;

use App\Models\Submission;

interface ISubmissionRepository
{
	public function findById(int $submissionId): ?Submission;

	/** @return Submission[] */
	public function findAll(): array;

	/** @return Submission[] */
	public function findByGigId(int $gigId): array;

	/** @return Submission[] */
	public function findByFreelancerId(int $freelancerId): array;

	public function create(array $data): int;

	public function update(int $submissionId, array $data): bool;

	public function delete(int $submissionId): bool;
}

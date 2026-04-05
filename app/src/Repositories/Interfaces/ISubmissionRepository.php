<?php

namespace App\Repositories\Interfaces;

use App\Models\Submission;

interface ISubmissionRepository
{
	/**
	 * Find a submission by primary key.
	 */
	public function findById(int $submissionId): ?Submission;

	/**
	 * Return all submission records.
	 */
	public function findAll(): array;

	/**
	 * Find submissions for a specific gig.
	 */
	public function findByGigId(int $gigId): array;

	/**
	 * Find submissions for a specific freelancer.
	 */
	public function findByFreelancerId(int $freelancerId): array;

	/**
	 * Create a submission record.
	 */
	public function create(array $data): int;

	/**
	 * Update a submission record.
	 */
	public function update(int $submissionId, array $data): bool;

	/**
	 * Delete a submission record.
	 */
	public function delete(int $submissionId): bool;
}

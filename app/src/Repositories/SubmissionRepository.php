<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Submission;
use App\Repositories\Interfaces\ISubmissionRepository;

class SubmissionRepository extends Repository implements ISubmissionRepository
{
	/**
	 * Return the backing submission table name.
	 */
	protected function tableName(): string
	{
		return 'submission';
	}

	/**
	 * Return the primary key column for submissions.
	 */
	protected function primaryKey(): string
	{
		return 'submissionId';
	}

	/**
	 * Find a submission by id.
	 */
	public function findById(int $submissionId): ?Submission
	{
		$row = $this->findRowById($submissionId);

		return $row !== null ? $this->mapRowToModel($row) : null;
	}

	/**
	 * Return all submissions.
	 */
	public function findAll(): array
	{
		$rows = $this->findAllRowsFromTable();

		return array_map(fn(array $row): Submission => $this->mapRowToModel($row), $rows);
	}

	/**
	 * Return submissions for a gig.
	 */
	public function findByGigId(int $gigId): array
	{
		$rows = $this->fetchAllRows('SELECT * FROM submission WHERE gigId = :gigId', ['gigId' => $gigId]);

		return array_map(fn(array $row): Submission => $this->mapRowToModel($row), $rows);
	}

	/**
	 * Find a submission for a gig/freelancer pair.
	 */
	public function findByGigIdAndFreelancerId(int $gigId, int $freelancerId): ?Submission
	{
		$row = $this->fetchOneRow(
			'SELECT * FROM submission WHERE gigId = :gigId AND freelancerId = :freelancerId LIMIT 1',
			[
				'gigId' => $gigId,
				'freelancerId' => $freelancerId,
			]
		);

		return $row !== null ? $this->mapRowToModel($row) : null;
	}

	/**
	 * Return submissions for a freelancer.
	 */
	public function findByFreelancerId(int $freelancerId): array
	{
		$rows = $this->fetchAllRows('SELECT * FROM submission WHERE freelancerId = :freelancerId', ['freelancerId' => $freelancerId]);

		return array_map(fn(array $row): Submission => $this->mapRowToModel($row), $rows);
	}

	/**
	 * Create a submission record.
	 */
	public function create(array $data): int
	{
		return $this->insertAndReturnId(
			'INSERT INTO submission (gigId, freelancerId, status)
			 VALUES (:gigId, :freelancerId, :status)',
			[
				'gigId' => $data['gigId'],
				'freelancerId' => $data['freelancerId'],
				'status' => $data['status'],
			]
		);
	}

	/**
	 * Update a submission record.
	 */
	public function update(int $submissionId, array $data): bool
	{
		return $this->executeStatement(
			'UPDATE submission
			 SET gigId = :gigId,
				 freelancerId = :freelancerId,
				 status = :status
			 WHERE submissionId = :submissionId',
			[
				'submissionId' => $submissionId,
				'gigId' => $data['gigId'],
				'freelancerId' => $data['freelancerId'],
				'status' => $data['status'],
			]
		);
	}

	/**
	 * Delete a submission record.
	 */
	public function delete(int $submissionId): bool
	{
		return $this->deleteRowById($submissionId);
	}

	/**
	 * Map a database row to a Submission model.
	 */
	private function mapRowToModel(array $row): Submission
	{
		return new Submission(
			(int) $row['submissionId'],
			(int) $row['gigId'],
			(int) $row['freelancerId'],
			(string) $row['status'],
			(string) $row['submittedAt'],
		);
	}
}

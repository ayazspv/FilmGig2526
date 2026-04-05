<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Submission;
use App\Repositories\Interfaces\ISubmissionRepository;

class SubmissionRepository extends Repository implements ISubmissionRepository
{
	protected function tableName(): string
	{
		return 'submission';
	}

	protected function primaryKey(): string
	{
		return 'submissionId';
	}

	public function findById(int $submissionId): ?Submission
	{
		$row = $this->findRowById($submissionId);

		return $row !== null ? $this->mapRowToModel($row) : null;
	}

	public function findAll(): array
	{
		$rows = $this->findAllRowsFromTable();

		return array_map(fn(array $row): Submission => $this->mapRowToModel($row), $rows);
	}

	public function findByGigId(int $gigId): array
	{
		$rows = $this->fetchAllRows('SELECT * FROM submission WHERE gigId = :gigId', ['gigId' => $gigId]);

		return array_map(fn(array $row): Submission => $this->mapRowToModel($row), $rows);
	}

	public function findByFreelancerId(int $freelancerId): array
	{
		$rows = $this->fetchAllRows('SELECT * FROM submission WHERE freelancerId = :freelancerId', ['freelancerId' => $freelancerId]);

		return array_map(fn(array $row): Submission => $this->mapRowToModel($row), $rows);
	}

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

	public function delete(int $submissionId): bool
	{
		return $this->deleteRowById($submissionId);
	}

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

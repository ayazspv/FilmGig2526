<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Gig;
use App\Repositories\Interfaces\IGigRepository;

class GigRepository extends Repository implements IGigRepository
{
	protected function tableName(): string
	{
		return 'gig';
	}

	protected function primaryKey(): string
	{
		return 'gigId';
	}

	public function findById(int $gigId): ?Gig
	{
		$row = $this->findRowById($gigId);

		return $row !== null ? $this->mapRowToModel($row) : null;
	}

	public function findAll(): array
	{
		$rows = $this->findAllRowsFromTable();

		return array_map(fn(array $row): Gig => $this->mapRowToModel($row), $rows);
	}

	public function findByOwnerId(int $ownerId): array
	{
		$rows = $this->fetchAllRows('SELECT * FROM gig WHERE ownerId = :ownerId', ['ownerId' => $ownerId]);

		return array_map(fn(array $row): Gig => $this->mapRowToModel($row), $rows);
	}

	public function create(array $data): int
	{
		return $this->insertAndReturnId(
			'INSERT INTO gig (ownerId, title, description, category, location, startDate, rateType, payRate, status)
			 VALUES (:ownerId, :title, :description, :category, :location, :startDate, :rateType, :payRate, :status)',
			[
				'ownerId' => $data['ownerId'],
				'title' => $data['title'],
				'description' => $data['description'],
				'category' => $data['category'],
				'location' => $data['location'],
				'startDate' => $data['startDate'],
				'rateType' => $data['rateType'],
				'payRate' => $data['payRate'],
				'status' => $data['status'],
			]
		);
	}

	public function update(int $gigId, array $data): bool
	{
		return $this->executeStatement(
			'UPDATE gig
			 SET ownerId = :ownerId,
				 title = :title,
				 description = :description,
				 category = :category,
				 location = :location,
				 startDate = :startDate,
				 rateType = :rateType,
				 payRate = :payRate,
				 status = :status
			 WHERE gigId = :gigId',
			[
				'gigId' => $gigId,
				'ownerId' => $data['ownerId'],
				'title' => $data['title'],
				'description' => $data['description'],
				'category' => $data['category'],
				'location' => $data['location'],
				'startDate' => $data['startDate'],
				'rateType' => $data['rateType'],
				'payRate' => $data['payRate'],
				'status' => $data['status'],
			]
		);
	}

	public function delete(int $gigId): bool
	{
		return $this->deleteRowById($gigId);
	}

	private function mapRowToModel(array $row): Gig
	{
		$gig = new Gig();
		$gig->setGigId((int) $row['gigId']);
		$gig->setOwnerId((int) $row['ownerId']);
		$gig->setTitle((string) $row['title']);
		$gig->setDescription((string) $row['description']);
		$gig->setCategory((string) $row['category']);
		$gig->setLocation((string) $row['location']);
		$gig->setStartDate((string) $row['startDate']);
		$gig->setRateType((string) $row['rateType']);
		$gig->setPayRate((float) $row['payRate']);
		$gig->setStatus((string) $row['status']);
		$gig->setCreatedAt((string) $row['createdAt']);

		return $gig;
	}
}

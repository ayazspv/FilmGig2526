<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Gig;
use App\Repositories\Interfaces\IGigRepository;

class GigRepository extends Repository implements IGigRepository
{
	/**
	 * Return the backing gig table name.
	 */
	protected function tableName(): string
	{
		return 'gig';
	}

	/**
	 * Return the primary key column for gigs.
	 */
	protected function primaryKey(): string
	{
		return 'gigId';
	}

	/**
	 * Find a gig by id.
	 */
	public function findById(int $gigId): ?Gig
	{
		$row = $this->findRowById($gigId);

		return $row !== null ? $this->mapRowToModel($row) : null;
	}

	/**
	 * Return all gigs.
	 */
	public function findAll(): array
	{
		$rows = $this->findAllRowsFromTable();

		return array_map(fn(array $row): Gig => $this->mapRowToModel($row), $rows);
	}

	/**
	 * Return all gigs owned by a user.
	 */
	public function findByOwnerId(int $ownerId): array
	{
		$rows = $this->fetchAllRows('SELECT * FROM gig WHERE ownerId = :ownerId', ['ownerId' => $ownerId]);

		return array_map(fn(array $row): Gig => $this->mapRowToModel($row), $rows);
	}

	/**
	 * Create a gig record.
	 */
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

	/**
	 * Update a gig record.
	 */
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

	/**
	 * Delete a gig record.
	 */
	public function delete(int $gigId): bool
	{
		return $this->deleteRowById($gigId);
	}

	/**
	 * Map a database row to a Gig model.
	 */
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

<?php

namespace App\Framework;

use PDO;
use PDOStatement;

abstract class Repository
{
	/**
	 * Create a repository bound to a PDO connection.
	 */
	public function __construct(protected readonly PDO $pdo)
	{
	}

	/**
	 * Return the table name used by the concrete repository.
	 */
	abstract protected function tableName(): string;

	/**
	 * Return the primary key column used by the concrete repository.
	 */
	abstract protected function primaryKey(): string;

	/**
	 * Fetch a single row from a custom SQL query.
	 */
	protected function fetchOneRow(string $sql, array $params = []): ?array
	{
		$statement = $this->prepareAndExecute($sql, $params);
		$row = $statement->fetch(PDO::FETCH_ASSOC);

		return $row !== false ? $row : null;
	}

	/**
	 * Fetch all rows from a custom SQL query.
	 */
	protected function fetchAllRows(string $sql, array $params = []): array
	{
		$statement = $this->prepareAndExecute($sql, $params);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Execute a write statement and return whether it succeeded.
	 */
	protected function executeStatement(string $sql, array $params = []): bool
	{
		return $this->prepareAndExecute($sql, $params)->rowCount() >= 0;
	}

	/**
	 * Execute an insert and return the last inserted identifier.
	 */
	protected function insertAndReturnId(string $sql, array $params = []): int
	{
		$this->prepareAndExecute($sql, $params);

		return (int) $this->pdo->lastInsertId();
	}

	/**
	 * Find a single row by the repository primary key.
	 */
	protected function findRowById(int $id): ?array
	{
		$table = $this->tableName();
		$primaryKey = $this->primaryKey();

		return $this->fetchOneRow(
			sprintf('SELECT * FROM %s WHERE %s = :id LIMIT 1', $table, $primaryKey),
			['id' => $id]
		);
	}

	/**
	 * Load every row from the configured repository table.
	 */
	protected function findAllRowsFromTable(): array
	{
		$table = $this->tableName();

		return $this->fetchAllRows(sprintf('SELECT * FROM %s', $table));
	}

	/**
	 * Delete a row by its primary key.
	 */
	protected function deleteRowById(int $id): bool
	{
		$table = $this->tableName();
		$primaryKey = $this->primaryKey();

		return $this->executeStatement(
			sprintf('DELETE FROM %s WHERE %s = :id', $table, $primaryKey),
			['id' => $id]
		);
	}

	/**
	 * Prepare and execute a SQL statement.
	 */
	private function prepareAndExecute(string $sql, array $params = []): PDOStatement
	{
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement;
	}
}

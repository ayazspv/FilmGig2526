<?php

namespace App\Framework;

use PDO;
use PDOStatement;

abstract class Repository
{
	public function __construct(protected readonly PDO $pdo)
	{
	}

	abstract protected function tableName(): string;

	abstract protected function primaryKey(): string;

	protected function fetchOneRow(string $sql, array $params = []): ?array
	{
		$statement = $this->prepareAndExecute($sql, $params);
		$row = $statement->fetch(PDO::FETCH_ASSOC);

		return $row !== false ? $row : null;
	}

	protected function fetchAllRows(string $sql, array $params = []): array
	{
		$statement = $this->prepareAndExecute($sql, $params);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	protected function executeStatement(string $sql, array $params = []): bool
	{
		return $this->prepareAndExecute($sql, $params)->rowCount() >= 0;
	}

	protected function insertAndReturnId(string $sql, array $params = []): int
	{
		$this->prepareAndExecute($sql, $params);

		return (int) $this->pdo->lastInsertId();
	}

	protected function findRowById(int $id): ?array
	{
		$table = $this->tableName();
		$primaryKey = $this->primaryKey();

		return $this->fetchOneRow(
			sprintf('SELECT * FROM %s WHERE %s = :id LIMIT 1', $table, $primaryKey),
			['id' => $id]
		);
	}

	protected function findAllRowsFromTable(): array
	{
		$table = $this->tableName();

		return $this->fetchAllRows(sprintf('SELECT * FROM %s', $table));
	}

	protected function deleteRowById(int $id): bool
	{
		$table = $this->tableName();
		$primaryKey = $this->primaryKey();

		return $this->executeStatement(
			sprintf('DELETE FROM %s WHERE %s = :id', $table, $primaryKey),
			['id' => $id]
		);
	}

	private function prepareAndExecute(string $sql, array $params = []): PDOStatement
	{
		$statement = $this->pdo->prepare($sql);
		$statement->execute($params);

		return $statement;
	}
}

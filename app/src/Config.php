<?php

namespace App;

use PDO;
use PDOException;

class Config
{
	public static function pdo(): PDO
	{
		$host = self::env('DB_HOST', 'mysql');
		$port = self::env('DB_PORT', '3306');
		$database = self::env('DB_DATABASE', 'developmentdb');
		$username = self::env('DB_USERNAME', 'developer');
		$password = self::env('DB_PASSWORD', 'secret123');

		$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

		try {
			$pdo = new PDO($dsn, $username, $password);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

			return $pdo;
		} catch (PDOException $exception) {
			throw new PDOException('Unable to connect to the database: ' . $exception->getMessage(), (int) $exception->getCode(), $exception);
		}
	}

	private static function env(string $key, string $default): string
	{
		$value = getenv($key);

		if ($value === false || $value === '') {
			return $default;
		}

		return $value;
	}
}

<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface IUserRepository
{
	public function findById(int $userId): ?User;

	/** @return User[] */
	public function findAll(): array;

	public function findByUsername(string $username): ?User;

	public function usernameExists(string $username): bool;

	public function findByEmail(string $email): ?User;

	public function emailExists(string $email): bool;

	public function kvkNrExists(string $kvkNr): bool;

	public function create(array $data): int;

	public function updatePassword(int $userId, string $passwordHash): bool;

	public function update(int $userId, array $data): bool;

	public function delete(int $userId): bool;
}

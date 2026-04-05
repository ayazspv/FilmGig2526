<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface IUserRepository
{
	/**
	 * Find a user by primary key.
	 */
	public function findById(int $userId): ?User;

	/**
	 * Return all users.
	 */
	public function findAll(): array;

	/**
	 * Find a user by username.
	 */
	public function findByUsername(string $username): ?User;

	/**
	 * Check whether a username already exists.
	 */
	public function usernameExists(string $username): bool;

	/**
	 * Find a user by email address.
	 */
	public function findByEmail(string $email): ?User;

	/**
	 * Check whether an email address already exists.
	 */
	public function emailExists(string $email): bool;

	/**
	 * Check whether a KVK number already exists.
	 */
	public function kvkNrExists(string $kvkNr): bool;

	/**
	 * Create a new user record.
	 */
	public function create(array $data): int;

	/**
	 * Update only the stored password hash.
	 */
	public function updatePassword(int $userId, string $passwordHash): bool;

	/**
	 * Update a full user record.
	 */
	public function update(int $userId, array $data): bool;

	/**
	 * Delete a user record.
	 */
	public function delete(int $userId): bool;
}

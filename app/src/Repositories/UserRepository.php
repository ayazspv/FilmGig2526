<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;

class UserRepository extends Repository implements IUserRepository
{
	/**
	 * Return the backing user table name.
	 */
    protected function tableName(): string
    {
        return '`user`';
    }

	/**
	 * Return the primary key column for users.
	 */
    protected function primaryKey(): string
    {
        return 'userId';
    }

	/**
	 * Find a user by id.
	 */
    public function findById(int $userId): ?User
    {
        $row = $this->findRowById($userId);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

	/**
	 * Return all users.
	 */
    public function findAll(): array
    {
        $rows = $this->findAllRowsFromTable();

        return array_map(fn(array $row): User => $this->mapRowToModel($row), $rows);
    }

	/**
	 * Check whether an email exists.
	 */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

	/**
	 * Check whether a KVK number exists.
	 */
    public function kvkNrExists(string $kvkNr): bool
    {
        $row = $this->fetchOneRow('SELECT userId FROM `user` WHERE kvkNr = :kvkNr LIMIT 1', ['kvkNr' => $kvkNr]);

        return $row !== null;
    }

	/**
	 * Find a user by username.
	 */
    public function findByUsername(string $username): ?User
    {
        $row = $this->fetchOneRow('SELECT * FROM `user` WHERE username = :username LIMIT 1', ['username' => $username]);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

	/**
	 * Check whether a username exists.
	 */
    public function usernameExists(string $username): bool
    {
        return $this->findByUsername($username) !== null;
    }

	/**
	 * Find a user by email address.
	 */
    public function findByEmail(string $email): ?User
    {
        $row = $this->fetchOneRow('SELECT * FROM `user` WHERE email = :email LIMIT 1', ['email' => $email]);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

	/**
	 * Create a new user record.
	 */
    public function create(array $data): int
    {
        return $this->insertAndReturnId(
            'INSERT INTO `user` (username, name, email, password, role, address, bio, kvkNr)
             VALUES (:username, :name, :email, :password, :role, :address, :bio, :kvkNr)',
            [
                'username' => $data['username'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
                'address' => $data['address'] ?? '',
                'bio' => $data['bio'] ?? '',
                'kvkNr' => $data['kvkNr'],
            ]
        );
    }

	/**
	 * Update a user's password hash.
	 */
    public function updatePassword(int $userId, string $passwordHash): bool
    {
        return $this->executeStatement(
            'UPDATE `user` SET password = :password WHERE userId = :userId',
            [
                'userId' => $userId,
                'password' => $passwordHash,
            ]
        );
    }

	/**
	 * Update a full user record.
	 */
    public function update(int $userId, array $data): bool
    {
        return $this->executeStatement(
            'UPDATE `user`
             SET username = :username,
                 name = :name,
                 email = :email,
                 password = :password,
                 role = :role,
                 address = :address,
                 bio = :bio,
                 kvkNr = :kvkNr
             WHERE userId = :userId',
            [
                'userId' => $userId,
                'username' => $data['username'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
                'address' => $data['address'] ?? '',
                'bio' => $data['bio'] ?? '',
                'kvkNr' => $data['kvkNr'],
            ]
        );
    }

	/**
	 * Delete a user record.
	 */
    public function delete(int $userId): bool
    {
        return $this->deleteRowById($userId);
    }

	/**
	 * Map a database row to a User model.
	 */
    private function mapRowToModel(array $row): User
    {
        return new User(
            (int) $row['userId'],
            (string) $row['username'],
            (string) $row['name'],
            (string) $row['email'],
            (string) $row['password'],
            (string) $row['role'],
            (string) ($row['address'] ?? ''),
            (string) ($row['bio'] ?? ''),
            (int) $row['kvkNr'],
            (string) $row['createdAt'],
        );
    }
}

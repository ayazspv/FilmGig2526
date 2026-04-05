<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\User;
use App\Repositories\Interfaces\IUserRepository;

class UserRepository extends Repository implements IUserRepository
{
    protected function tableName(): string
    {
        return '`user`';
    }

    protected function primaryKey(): string
    {
        return 'userId';
    }

    public function findById(int $userId): ?User
    {
        $row = $this->findRowById($userId);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

    public function findAll(): array
    {
        $rows = $this->findAllRowsFromTable();

        return array_map(fn(array $row): User => $this->mapRowToModel($row), $rows);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function kvkNrExists(string $kvkNr): bool
    {
        $row = $this->fetchOneRow('SELECT userId FROM `user` WHERE kvkNr = :kvkNr LIMIT 1', ['kvkNr' => $kvkNr]);

        return $row !== null;
    }

    public function findByEmail(string $email): ?User
    {
        $row = $this->fetchOneRow('SELECT * FROM `user` WHERE email = :email LIMIT 1', ['email' => $email]);

        return $row !== null ? $this->mapRowToModel($row) : null;
    }

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

    public function delete(int $userId): bool
    {
        return $this->deleteRowById($userId);
    }

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

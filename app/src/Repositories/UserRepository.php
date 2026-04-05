<?php

namespace App\Repositories;

use PDO;

class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function kvkNrExists(string $kvkNr): bool
    {
        $statement = $this->pdo->prepare('SELECT userId FROM user WHERE kvkNr = :kvkNr LIMIT 1');
        $statement->execute(['kvkNr' => $kvkNr]);

        return (bool) $statement->fetchColumn();
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM user WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);

        $user = $statement->fetch();

        return $user ?: null;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO user (username, name, email, password, role, address, bio, kvkNr)
             VALUES (:username, :name, :email, :password, :role, :address, :bio, :kvkNr)'
        );

        $statement->execute([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'address' => $data['address'],
            'bio' => $data['bio'],
            'kvkNr' => $data['kvkNr'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}

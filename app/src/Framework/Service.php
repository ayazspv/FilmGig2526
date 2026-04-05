<?php

namespace App\Framework;

use PDO;

abstract class Service
{
    /**
     * Store the shared database connection for the service.
     */
    public function __construct(protected readonly PDO $pdo)
    {
    }
}

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

    /**
     * Build a standardized failure payload with optional input values.
     */
    protected function buildFailureResult(array $errors, array $input = []): array
    {
        $result = [
            'success' => false,
            'errors' => $errors,
        ];

        if (func_num_args() >= 2) {
            $result['input'] = $input;
        }

        return $result;
    }

    /**
     * Build a standardized failure payload from a general message.
     */
    protected function buildGeneralFailure(string $message, array $input = []): array
    {
        return $this->buildFailureResult(['general' => $message], $input);
    }

    /**
     * Convert an image MIME type to a file extension.
     */
    protected function imageMimeTypeToExtension(string $mimeType): ?string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => null,
        };
    }
}

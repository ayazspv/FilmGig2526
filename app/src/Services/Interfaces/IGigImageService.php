<?php

namespace App\Services\Interfaces;

interface IGigImageService
{
    /**
     * Validate an uploaded gig image file before saving.
     */
    public function validateUpload(array $file, bool $required = true): array;

    /**
     * Save an uploaded gig image to disk.
     */
    public function save(array $file, ?string $fallbackImageUrl = null): ?string;

    /**
     * Delete a gig image file from disk.
     */
    public function delete(?string $imageUrl): bool;
}

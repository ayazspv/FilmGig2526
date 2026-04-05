<?php

namespace App\Services\Interfaces;

/**
 * IGigImageService defines the contract for gig image file operations.
 */
interface IGigImageService
{
    /**
     * Validate an uploaded gig image file before saving.
     *
     * @param array $file Upload file array from $_FILES
     * @param bool $required Whether the image is required
     * @return array Validation errors (empty if valid)
     */
    public function validateUpload(array $file, bool $required = true): array;

    /**
     * Save an uploaded gig image to disk.
     *
     * @param array $file Upload file array from $_FILES
     * @param string|null $fallbackImageUrl URL to return if save fails
     * @return string|null Saved image URL or fallback URL
     */
    public function save(array $file, ?string $fallbackImageUrl = null): ?string;

    /**
     * Delete a gig image file from disk.
     *
     * @param string|null $imageUrl URL of the image to delete
     * @return bool True if deleted or no action needed, false if delete failed
     */
    public function delete(?string $imageUrl): bool;
}

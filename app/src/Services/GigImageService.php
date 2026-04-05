<?php

namespace App\Services;

use App\Framework\Service;
use App\Services\Interfaces\IGigImageService;
use DateTimeImmutable;
use PDO;

/**
 * GigImageService handles all gig image file operations.
 * 
 * Responsibilities:
 * - Validate uploaded image files
 * - Save uploaded images to disk
 * - Delete image files from disk
 * - Convert MIME types to file extensions
 */
class GigImageService extends Service implements IGigImageService
{
    private const UPLOAD_DIRECTORY = __DIR__ . '/../../public/assets/images';
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB
    private const UPLOAD_PREFIX = 'gig-upload-';
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    /**
     * Build the gig image service.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Validate an uploaded gig image file before saving.
     * 
     * @param array $file Upload file array from $_FILES
     * @param bool $required Whether the image is required
     * @return array Validation errors (empty if valid)
     */
    public function validateUpload(array $file, bool $required = true): array
    {
        $errors = [];

        // Check if file was uploaded
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                $errors['image'] = 'Gig picture is required.';
            }
            return $errors;
        }

        // Check for server-side size limits
        if (($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_INI_SIZE || 
            ($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_FORM_SIZE) {
            $errors['image'] = 'Gig picture must be smaller than 5 MB.';
            return $errors;
        }

        // Check for upload errors
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors['image'] = 'The gig picture could not be uploaded.';
            return $errors;
        }

        // Check application-level file size
        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            $errors['image'] = 'Gig picture must be smaller than 5 MB.';
            return $errors;
        }

        // Validate that file is a valid image and check MIME type
        $errors = array_merge($errors, $this->validateImageContent((string) ($file['tmp_name'] ?? '')));

        return $errors;
    }

    /**
     * Save an uploaded gig image to disk.
     * 
     * @param array $file Upload file array from $_FILES
     * @param string|null $fallbackImageUrl URL to return if save fails
     * @return string|null Saved image URL or fallback URL
     */
    public function save(array $file, ?string $fallbackImageUrl = null): ?string
    {
        // If no file uploaded, return fallback
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $fallbackImageUrl;
        }

        // Validate the upload
        if (!empty($this->validateUpload($file, false))) {
            return $fallbackImageUrl;
        }

        // Get image MIME type and convert to extension
        $imageInfo = getimagesize((string) $file['tmp_name']);
        $extension = $this->mimeTypeToExtension((string) ($imageInfo['mime'] ?? ''));

        if ($extension === null) {
            return $fallbackImageUrl;
        }

        // Ensure upload directory exists
        if (!$this->ensureUploadDirectory()) {
            return $fallbackImageUrl;
        }

        // Generate unique filename and move uploaded file
        $fileName = sprintf('%s%s.%s', self::UPLOAD_PREFIX, bin2hex(random_bytes(8)), $extension);
        $targetPath = self::UPLOAD_DIRECTORY . '/' . $fileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            return $fallbackImageUrl;
        }

        return '/assets/images/' . $fileName;
    }

    /**
     * Delete a gig image file from disk.
     * 
     * @param string|null $imageUrl URL of the image to delete
     * @return bool True if deleted or no action needed, false if delete failed
     */
    public function delete(?string $imageUrl): bool
    {
        // Nothing to delete if URL is empty or null
        if ($imageUrl === null || $imageUrl === '') {
            return true;
        }

        // Only delete files that are in the assets/images directory
        if (!str_starts_with($imageUrl, '/assets/images/')) {
            return true;
        }

        // Only delete files with upload prefix (ignore seed images)
        $fileName = basename($imageUrl);
        if (!str_starts_with($fileName, self::UPLOAD_PREFIX)) {
            return true;
        }

        // Construct full path and delete if file exists
        $imagePath = __DIR__ . '/../../public' . $imageUrl;

        if (is_file($imagePath)) {
            return @unlink($imagePath);
        }

        return true;
    }

    /**
     * Validate image file content and MIME type.
     * 
     * @param string $filePath Path to the temporary uploaded file
     * @return array Validation errors (empty if valid)
     */
    private function validateImageContent(string $filePath): array
    {
        $errors = [];

        // Use getimagesize to verify it's a valid image
        $imageInfo = @getimagesize($filePath);

        if ($imageInfo === false) {
            $errors['image'] = 'Gig picture must be a valid image file.';
            return $errors;
        }

        // Verify MIME type is in allowed list
        $mimeType = (string) ($imageInfo['mime'] ?? '');

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            $errors['image'] = 'Gig picture must be JPG, PNG, GIF, or WEBP.';
        }

        return $errors;
    }

    /**
     * Convert an image MIME type to a file extension.
     * 
     * @param string $mimeType MIME type (e.g., 'image/jpeg')
     * @return string|null File extension (e.g., 'jpg') or null if unknown
     */
    private function mimeTypeToExtension(string $mimeType): ?string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => null,
        };
    }

    /**
     * Ensure the upload directory exists and is writable.
     * 
     * @return bool True if directory exists and is usable
     */
    private function ensureUploadDirectory(): bool
    {
        if (is_dir(self::UPLOAD_DIRECTORY)) {
            return true;
        }

        return mkdir(self::UPLOAD_DIRECTORY, 0775, true) && is_dir(self::UPLOAD_DIRECTORY);
    }
}

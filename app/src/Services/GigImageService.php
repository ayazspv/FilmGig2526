<?php

namespace App\Services;

use App\Framework\Service;
use App\Services\Interfaces\IGigImageService;
use PDO;

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
     */
    public function validateUpload(array $file, bool $required = true): array
    {
        $preValidationError = $this->validateUploadMetadata($file, $required);

        if ($preValidationError !== null) {
            return $preValidationError;
        }

        return $this->validateImageContent((string) ($file['tmp_name'] ?? ''));
    }

    /**
     * Save an uploaded gig image to disk.
     */
    public function save(array $file, ?string $fallbackImageUrl = null): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $fallbackImageUrl;
        }

        if (!empty($this->validateUpload($file, false))) {
            return $fallbackImageUrl;
        }

        $extension = $this->resolveUploadedFileExtension((string) ($file['tmp_name'] ?? ''));

        if ($extension === null) {
            return $fallbackImageUrl;
        }

        if (!$this->ensureUploadDirectory()) {
            return $fallbackImageUrl;
        }

        $targetPath = $this->buildUploadTargetPath($extension);

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            return $fallbackImageUrl;
        }

        return '/assets/images/' . basename($targetPath);
    }

    /**
     * Delete a gig image file from disk.
     */
    public function delete(?string $imageUrl): bool
    {
        if ($imageUrl === null || $imageUrl === '') {
            return true;
        }

        if (!str_starts_with($imageUrl, '/assets/images/')) {
            return true;
        }

        $fileName = basename($imageUrl);
        if (!str_starts_with($fileName, self::UPLOAD_PREFIX)) {
            return true;
        }

        $imagePath = __DIR__ . '/../../public' . $imageUrl;

        if (is_file($imagePath)) {
            return @unlink($imagePath);
        }

        return true;
    }

    /**
     * Validate image file content and MIME type.
     */
    private function validateImageContent(string $filePath): array
    {
        $errors = [];

        $imageInfo = @getimagesize($filePath);

        if ($imageInfo === false) {
            $errors['image'] = 'Gig picture must be a valid image file.';
            return $errors;
        }

        $mimeType = (string) ($imageInfo['mime'] ?? '');

        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            $errors['image'] = 'Gig picture must be JPG, PNG, GIF, or WEBP.';
        }

        return $errors;
    }

    /**
     * Ensure the upload directory exists.
     */
    private function ensureUploadDirectory(): bool
    {
        if (is_dir(self::UPLOAD_DIRECTORY)) {
            return true;
        }

        return mkdir(self::UPLOAD_DIRECTORY, 0775, true) && is_dir(self::UPLOAD_DIRECTORY);
    }

    /**
     * Validate upload metadata before inspecting image content.
     */
    private function validateUploadMetadata(array $file, bool $required): ?array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $required ? ['image' => 'Gig picture is required.'] : [];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_FORM_SIZE) {
            return ['image' => 'Gig picture must be smaller than 5 MB.'];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return ['image' => 'The gig picture could not be uploaded.'];
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return ['image' => 'Gig picture must be smaller than 5 MB.'];
        }

        return null;
    }

    /**
     * Resolve the extension for an uploaded image file.
     */
    private function resolveUploadedFileExtension(string $tmpPath): ?string
    {
        $imageInfo = getimagesize($tmpPath);

        return $this->imageMimeTypeToExtension((string) ($imageInfo['mime'] ?? ''));
    }

    /**
     * Build the full target path for an uploaded image.
     */
    private function buildUploadTargetPath(string $extension): string
    {
        $fileName = sprintf('%s%s.%s', self::UPLOAD_PREFIX, bin2hex(random_bytes(8)), $extension);

        return self::UPLOAD_DIRECTORY . '/' . $fileName;
    }
}

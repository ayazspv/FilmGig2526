<?php

namespace App\Services;

use App\Enums\GigCategory;
use App\Enums\GigRateType;
use App\Enums\GigStatus;
use App\Framework\Service;
use App\Repositories\GigRepository;
use App\Repositories\SubmissionRepository;
use App\Services\Interfaces\IGigService;
use DateTimeImmutable;
use PDO;
use Throwable;

/**
 * GigService handles all gig-related business logic and operations.
 * 
 * Responsibilities:
 * - Create new gigs with image uploads
 * - Update existing gigs with image replacement
 * - Delete gigs with image cleanup
 * - Validate gig input data
 * - Normalize gig input from user submissions
 */
class GigService extends Service implements IGigService
{
    private GigRepository $gigRepository;
    private SubmissionRepository $submissionRepository;
    private GigImageService $imageService;

    /**
     * Build the gig service with its dependencies.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->gigRepository = new GigRepository($pdo);
        $this->submissionRepository = new SubmissionRepository($pdo);
        $this->imageService = new GigImageService($pdo);
    }

    /**
     * Create a new gig with uploaded image.
     * 
     * @param int $ownerId The ID of the production house owner
     * @param array $input Normalized gig input data
     * @param array $imageFile Upload file array from $_FILES
     * @return array Service result with success status, errors, and data
     */
    public function create(int $ownerId, array $input, array $imageFile): array
    {
        // Normalize and validate input
        $normalized = $this->normalizeInput($input);
        $errors = $this->validateInput($normalized);

        if (!empty($errors)) {
            return $this->buildFailureResult($errors, $normalized);
        }

        // Validate and save image
        $imageErrors = $this->imageService->validateUpload($imageFile, true);

        if (!empty($imageErrors)) {
            return $this->buildFailureResult($imageErrors, $normalized);
        }

        $imageUrl = $this->imageService->save($imageFile);

        if ($imageUrl === null) {
            return $this->buildFailureResult(
                ['image' => 'Unable to save the gig picture right now.'],
                $normalized
            );
        }

        // Persist gig to database
        try {
            $gigId = $this->gigRepository->create([
                'ownerId' => $ownerId,
                'imageUrl' => $imageUrl,
                'title' => $normalized['title'],
                'description' => $normalized['description'],
                'category' => $normalized['category'],
                'location' => $normalized['location'],
                'startDate' => $normalized['startDate'],
                'rateType' => $normalized['rateType'],
                'payRate' => $normalized['payRate'],
                'status' => $normalized['status'],
            ]);

            return $this->buildSuccessResult(
                'Gig published successfully.',
                ['gigId' => $gigId]
            );
        } catch (Throwable $exception) {
            // Clean up uploaded image if database save fails
            $this->imageService->delete($imageUrl);

            return $this->buildFailureResult(
                ['general' => 'Unable to save this gig right now. Please try again later.'],
                $normalized
            );
        }
    }

    /**
     * Update an existing gig with optional image replacement.
     * 
     * @param int $gigId The gig ID to update
     * @param int $ownerId The owner ID for ownership verification
     * @param array $input Normalized gig input data
     * @param array $imageFile Upload file array from $_FILES
     * @param string|null $currentImageUrl Current image URL for cleanup
     * @return array Service result with success status and errors
     */
    public function update(int $gigId, int $ownerId, array $input, array $imageFile, ?string $currentImageUrl): array
    {
        // Verify gig exists and is owned by the user
        $gig = $this->gigRepository->findById($gigId);

        if ($gig === null || $gig->getOwnerId() !== $ownerId) {
            return $this->buildFailureResult(['general' => 'Gig not found or access denied.']);
        }

        // Normalize and validate input
        $normalized = $this->normalizeInput($input);
        $errors = $this->validateInput($normalized);

        if (!empty($errors)) {
            return $this->buildFailureResult($errors, $normalized);
        }

        // Handle image update
        $imageUrl = $currentImageUrl;

        if (($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            // User uploaded a new image
            $imageErrors = $this->imageService->validateUpload($imageFile, false);

            if (!empty($imageErrors)) {
                return $this->buildFailureResult($imageErrors, $normalized);
            }

            $newImageUrl = $this->imageService->save($imageFile, $currentImageUrl);

            if ($newImageUrl === null) {
                return $this->buildFailureResult(
                    ['image' => 'Unable to save the gig picture right now.'],
                    $normalized
                );
            }

            $imageUrl = $newImageUrl;
        }

        // Persist updates to database
        try {
            $this->gigRepository->update($gigId, [
                'ownerId' => $ownerId,
                'imageUrl' => $imageUrl,
                'title' => $normalized['title'],
                'description' => $normalized['description'],
                'category' => $normalized['category'],
                'location' => $normalized['location'],
                'startDate' => $normalized['startDate'],
                'rateType' => $normalized['rateType'],
                'payRate' => $normalized['payRate'],
                'status' => $normalized['status'],
            ]);

            // Clean up old image if it was replaced
            if ($imageUrl !== $currentImageUrl && $currentImageUrl !== null) {
                $this->imageService->delete($currentImageUrl);
            }

            return $this->buildSuccessResult('Gig updated successfully.');
        } catch (Throwable $exception) {
            // Clean up newly uploaded image if database update fails
            if ($imageUrl !== $currentImageUrl && $imageUrl !== null) {
                $this->imageService->delete($imageUrl);
            }

            return $this->buildFailureResult(
                ['general' => 'Unable to update this gig right now. Please try again later.'],
                $normalized
            );
        }
    }

    /**
     * Delete a gig and clean up its image.
     * 
     * @param int $gigId The gig ID to delete
     * @param int $ownerId The owner ID for ownership verification
     * @return array Service result with success status and errors
     */
    public function delete(int $gigId, int $ownerId): array
    {
        // Verify gig exists and is owned by the user
        $gig = $this->gigRepository->findById($gigId);

        if ($gig === null || $gig->getOwnerId() !== $ownerId) {
            return $this->buildFailureResult(['general' => 'Gig not found or access denied.']);
        }

        $hasApplicants = count($this->submissionRepository->findByGigId($gigId)) > 0;
        $isActiveGig = strtolower((string) $gig->getStatus()) === GigStatus::ACTIVE->value;

        if ($isActiveGig && $hasApplicants) {
            return $this->buildFailureResult([
                'general' => 'Active gigs with applicants cannot be deleted. Close the gig first or resolve submissions before deleting.',
            ]);
        }

        // Delete gig from database
        try {
            $this->gigRepository->delete($gigId);
            $this->imageService->delete($gig->getImageUrl());

            return $this->buildSuccessResult('Gig deleted successfully.');
        } catch (Throwable $exception) {
            return $this->buildFailureResult(
                ['general' => 'Unable to delete this gig right now. Please try again later.']
            );
        }
    }

    /**
     * Fetch all gigs owned by a production house.
     * 
     * @param int $ownerId The production house owner ID
     * @return array Array of gig models owned by this owner
     */
    public function findByOwnerId(int $ownerId): array
    {
        if ($ownerId <= 0) {
            return [];
        }

        return $this->gigRepository->findByOwnerId($ownerId);
    }

    /**
     * Fetch a gig by ID with ownership verification.
     * 
     * Returns null if gig not found or ownership verification fails,
     * allowing the controller to redirect appropriately.
     * 
     * @param int $gigId The gig ID to fetch
     * @param int $ownerId The owner ID to verify ownership
     * @return \App\Models\Gig|null The gig if found and owned by owner, null otherwise
     */
    public function findByIdAndVerifyOwnership(int $gigId, int $ownerId): ?\App\Models\Gig
    {
        if ($gigId <= 0 || $ownerId <= 0) {
            return null;
        }

        $gig = $this->gigRepository->findById($gigId);

        if ($gig === null || $gig->getOwnerId() !== $ownerId) {
            return null;
        }

        return $gig;
    }

    /**
     * Normalize raw gig input from form submission.
     * 
     * @param array $input Raw input data
     * @return array Normalized input
     */
    private function normalizeInput(array $input): array
    {
        return [
            'title' => trim((string) ($input['title'] ?? '')),
            'description' => trim((string) ($input['description'] ?? '')),
            'category' => trim((string) ($input['category'] ?? '')),
            'location' => trim((string) ($input['location'] ?? '')),
            'startDate' => trim((string) ($input['startDate'] ?? '')),
            'rateType' => strtolower(trim((string) ($input['rateType'] ?? ''))),
            'payRate' => trim((string) ($input['payRate'] ?? '')),
            'status' => strtolower(trim((string) ($input['status'] ?? ''))),
        ];
    }

    /**
     * Validate normalized gig input data.
     * 
     * @param array $input Normalized gig input
     * @return array Validation errors (empty if valid)
     */
    private function validateInput(array $input): array
    {
        $errors = [];

        // Validate title
        if ($input['title'] === '') {
            $errors['title'] = 'Gig title is required.';
        }

        // Validate description
        if ($input['description'] === '') {
            $errors['description'] = 'Description is required.';
        }

        // Validate category
        if (!GigCategory::isValid($input['category'])) {
            $errors['category'] = 'Please choose a valid category.';
        }

        // Validate location
        if ($input['location'] === '') {
            $errors['location'] = 'Location is required.';
        }

        // Validate start date
        if (!$this->isValidDate($input['startDate'])) {
            $errors['startDate'] = 'Start date must be a valid date in YYYY-MM-DD format.';
        }

        // Validate rate type
        if (!GigRateType::isValid($input['rateType'])) {
            $errors['rateType'] = 'Please choose a valid rate type.';
        }

        // Validate pay rate
        if (!is_numeric($input['payRate']) || (float) $input['payRate'] <= 0) {
            $errors['payRate'] = 'Pay rate must be a positive number.';
        }

        // Validate status
        if (!GigStatus::isValid($input['status'])) {
            $errors['status'] = 'Please choose a valid gig status.';
        }

        return $errors;
    }

    /**
     * Check whether a date string is valid YYYY-MM-DD format.
     * 
     * @param string $value Date string to validate
     * @return bool True if valid date in YYYY-MM-DD format
     */
    private function isValidDate(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

    /**
     * Build a success result array.
     * 
     * @param string $message Success message
     * @param array $data Additional data to return
     * @return array Result array with success=true
     */
    private function buildSuccessResult(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Build a failure result array.
     * 
     * @param array $errors Error messages keyed by field
     * @param array $input Input data to return for form repopulation
     * @return array Result array with success=false
     */
    private function buildFailureResult(array $errors, array $input = []): array
    {
        return [
            'success' => false,
            'errors' => $errors,
            'input' => $input,
        ];
    }
}

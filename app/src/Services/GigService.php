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
     * Create a new gig with an uploaded image.
     */
    public function create(int $ownerId, array $input, array $imageFile): array
    {
        $normalized = $this->normalizeCreateInput($input);
        $errors = $this->validateInput($normalized);

        if (!empty($errors)) {
            return $this->buildFailureResult($errors, $normalized);
        }

        $image = $this->resolveCreateImageUrl($imageFile, $normalized);

        if (($image['failure'] ?? null) !== null) {
            return $image['failure'];
        }

        return $this->persistCreateGig($ownerId, $normalized, (string) $image['imageUrl']);
    }

    /**
     * Update an existing gig with optional image replacement.
     */
    public function update(int $gigId, int $ownerId, array $input, array $imageFile, ?string $currentImageUrl): array
    {
        if (!$this->isOwnedGig($gigId, $ownerId)) {
            return $this->buildFailureResult(['general' => 'Gig not found or access denied.']);
        }

        $normalized = $this->normalizeInput($input);
        $errors = $this->validateInput($normalized);

        if (!empty($errors)) {
            return $this->buildFailureResult($errors, $normalized);
        }

        $image = $this->resolveUpdatedImageUrl($imageFile, $currentImageUrl, $normalized);

        if (($image['failure'] ?? null) !== null) {
            return $image['failure'];
        }

        return $this->persistUpdatedGig(
            $gigId,
            $ownerId,
            $normalized,
            $image['imageUrl'],
            $currentImageUrl,
            (bool) $image['replacedImage']
        );
    }

    /**
     * Delete a gig and clean up its image.
     */
    public function delete(int $gigId, int $ownerId): array
    {
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
     * Return all gigs owned by a production house.
     */
    public function findByOwnerId(int $ownerId): array
    {
        if ($ownerId <= 0) {
            return [];
        }

        return $this->gigRepository->findByOwnerId($ownerId);
    }

    /**
     * Return a gig by id only when ownership is valid.
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
     */
    private function validateInput(array $input): array
    {
        $errors = [];

        $this->validateRequiredTextFields($input, $errors);
        $this->validateEnumFields($input, $errors);
        $this->validateStartDate($input, $errors);
        $this->validatePayRate($input, $errors);

        return $errors;
    }

    /**
     * Normalize creation input and force active status.
     */
    private function normalizeCreateInput(array $input): array
    {
        $normalized = $this->normalizeInput($input);
        $normalized['status'] = GigStatus::ACTIVE->value;

        return $normalized;
    }

    /**
     * Resolve and validate the image URL for a create operation.
     */
    private function resolveCreateImageUrl(array $imageFile, array $normalized): array
    {
        $errors = $this->imageService->validateUpload($imageFile, true);

        if (!empty($errors)) {
            return ['failure' => $this->buildFailureResult($errors, $normalized), 'imageUrl' => null];
        }

        $imageUrl = $this->imageService->save($imageFile);

        if ($imageUrl === null) {
            return [
                'failure' => $this->buildFailureResult(['image' => 'Unable to save the gig picture right now.'], $normalized),
                'imageUrl' => null,
            ];
        }

        return ['failure' => null, 'imageUrl' => $imageUrl];
    }

    /**
     * Persist a new gig and clean up uploaded files on failure.
     */
    private function persistCreateGig(int $ownerId, array $normalized, string $imageUrl): array
    {
        try {
            $gigId = $this->gigRepository->create($this->buildGigPayload($ownerId, $normalized, $imageUrl));

            return $this->buildSuccessResult('Gig published successfully.', ['gigId' => $gigId]);
        } catch (Throwable $exception) {
            $this->imageService->delete($imageUrl);

            return $this->buildFailureResult(
                ['general' => 'Unable to save this gig right now. Please try again later.'],
                $normalized
            );
        }
    }

    /**
     * Resolve and validate ownership for a gig.
     */
    private function isOwnedGig(int $gigId, int $ownerId): bool
    {
        $gig = $this->gigRepository->findById($gigId);

        return $gig !== null && $gig->getOwnerId() === $ownerId;
    }

    /**
     * Resolve and validate the image URL for an update operation.
     */
    private function resolveUpdatedImageUrl(array $imageFile, ?string $currentImageUrl, array $normalized): array
    {
        if (($imageFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['failure' => null, 'imageUrl' => $currentImageUrl, 'replacedImage' => false];
        }

        $errors = $this->imageService->validateUpload($imageFile, false);

        if (!empty($errors)) {
            return ['failure' => $this->buildFailureResult($errors, $normalized), 'imageUrl' => null, 'replacedImage' => false];
        }

        $newImageUrl = $this->imageService->save($imageFile, $currentImageUrl);

        if ($newImageUrl === null) {
            return [
                'failure' => $this->buildFailureResult(['image' => 'Unable to save the gig picture right now.'], $normalized),
                'imageUrl' => null,
                'replacedImage' => false,
            ];
        }

        return ['failure' => null, 'imageUrl' => $newImageUrl, 'replacedImage' => $newImageUrl !== $currentImageUrl];
    }

    /**
     * Persist an updated gig and clean up replaced files.
     */
    private function persistUpdatedGig(
        int $gigId,
        int $ownerId,
        array $normalized,
        ?string $imageUrl,
        ?string $currentImageUrl,
        bool $replacedImage
    ): array {
        try {
            $this->gigRepository->update($gigId, $this->buildGigPayload($ownerId, $normalized, $imageUrl));

            if ($replacedImage && $currentImageUrl !== null) {
                $this->imageService->delete($currentImageUrl);
            }

            return $this->buildSuccessResult('Gig updated successfully.');
        } catch (Throwable $exception) {
            if ($replacedImage && $imageUrl !== null) {
                $this->imageService->delete($imageUrl);
            }

            return $this->buildFailureResult(
                ['general' => 'Unable to update this gig right now. Please try again later.'],
                $normalized
            );
        }
    }

    /**
     * Build a repository payload from normalized gig data.
     */
    private function buildGigPayload(int $ownerId, array $normalized, ?string $imageUrl): array
    {
        return [
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
        ];
    }

    /**
     * Validate required text fields for gig input.
     */
    private function validateRequiredTextFields(array $input, array &$errors): void
    {
        if ($input['title'] === '') {
            $errors['title'] = 'Gig title is required.';
        }

        if ($input['description'] === '') {
            $errors['description'] = 'Description is required.';
        }

        if ($input['location'] === '') {
            $errors['location'] = 'Location is required.';
        }
    }

    /**
     * Validate enum-backed fields for gig input.
     */
    private function validateEnumFields(array $input, array &$errors): void
    {
        if (!GigCategory::isValid($input['category'])) {
            $errors['category'] = 'Please choose a valid category.';
        }

        if (!GigRateType::isValid($input['rateType'])) {
            $errors['rateType'] = 'Please choose a valid rate type.';
        }

        if (!GigStatus::isValid($input['status'])) {
            $errors['status'] = 'Please choose a valid gig status.';
        }
    }

    /**
     * Validate the gig start date.
     */
    private function validateStartDate(array $input, array &$errors): void
    {
        if (!$this->isValidDate($input['startDate'])) {
            $errors['startDate'] = 'Start date must be a valid date in YYYY-MM-DD format.';
        }
    }

    /**
     * Validate the gig pay rate.
     */
    private function validatePayRate(array $input, array &$errors): void
    {
        if (!is_numeric($input['payRate']) || (float) $input['payRate'] <= 0) {
            $errors['payRate'] = 'Pay rate must be a positive number.';
        }
    }

    /**
     * Check whether a date string is valid YYYY-MM-DD format.
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
     * Build a success result payload.
     */
    private function buildSuccessResult(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

}

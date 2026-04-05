<?php

namespace App\Services\Interfaces;

/**
 * IGigService defines the contract for gig business logic operations.
 */
interface IGigService
{
    /**
     * Create a new gig with uploaded image.
     *
     * @param int $ownerId The ID of the production house owner
     * @param array $input Normalized gig input data
     * @param array $imageFile Upload file array from $_FILES
     * @return array Service result with success status, errors, and data
     */
    public function create(int $ownerId, array $input, array $imageFile): array;

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
    public function update(int $gigId, int $ownerId, array $input, array $imageFile, ?string $currentImageUrl): array;

    /**
     * Delete a gig and clean up its image.
     *
     * @param int $gigId The gig ID to delete
     * @param int $ownerId The owner ID for ownership verification
     * @return array Service result with success status and errors
     */
    public function delete(int $gigId, int $ownerId): array;

    /**
     * Fetch all gigs owned by a production house.
     *
     * @param int $ownerId The production house owner ID
     * @return array Array of gig models owned by this owner
     */
    public function findByOwnerId(int $ownerId): array;

    /**
     * Fetch a gig by ID with ownership verification.
     *
     * Returns null if gig not found or ownership verification fails.
     *
     * @param int $gigId The gig ID to fetch
     * @param int $ownerId The owner ID to verify ownership
     * @return \App\Models\Gig|null The gig if found and owned by owner, null otherwise
     */
    public function findByIdAndVerifyOwnership(int $gigId, int $ownerId): ?\App\Models\Gig;
}

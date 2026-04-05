<?php

namespace App\Services\Interfaces;

interface IGigService
{
    /**
     * Create a new gig with an uploaded image.
     */
    public function create(int $ownerId, array $input, array $imageFile): array;

    /**
     * Update an existing gig with optional image replacement.
     */
    public function update(int $gigId, int $ownerId, array $input, array $imageFile, ?string $currentImageUrl): array;

    /**
     * Delete a gig and clean up its image.
     */
    public function delete(int $gigId, int $ownerId): array;

    /**
     * Return all gigs owned by a production house.
     */
    public function findByOwnerId(int $ownerId): array;

    /**
     * Return a gig by id only when ownership is valid.
     */
    public function findByIdAndVerifyOwnership(int $gigId, int $ownerId): ?\App\Models\Gig;
}

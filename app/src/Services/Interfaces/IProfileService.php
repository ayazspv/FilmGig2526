<?php

namespace App\Services\Interfaces;

interface IProfileService
{
    /**
     * Return editable profile payload for admin/production house users.
     */
    public function getAdminProfileData(int $userId): array;

    /**
     * Return editable profile payload for freelancer users.
     */
    public function getFreelancerProfileData(int $userId): array;

    /**
     * Persist profile updates for admin/production house users.
     */
    public function updateAdminProfile(int $userId, array $input, array $profileImageFile): array;

    /**
     * Persist profile updates for freelancer users.
     */
    public function updateFreelancerProfile(int $userId, array $input, array $profileImageFile): array;
}

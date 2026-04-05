<?php

namespace App\Controllers;

use App\Config;
use App\Enums\RoleType;
use App\Framework\Controller;
use App\Models\Gig;
use App\Repositories\GigRepository;
use App\ViewModels\AdminDashboardViewModel;
use App\ViewModels\FreelancerDashboardViewModel;
use App\ViewModels\AdminGigPostingViewModel;
use App\ViewModels\AdminGigEditingViewModel;
use App\ViewModels\AdminGigListingViewModel;
use DateTimeImmutable;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Route authenticated users to the correct dashboard by role.
     */
    public function showDashboard(array $params = []): void
    {
        $this->requireAuthentication();

        $role = $this->authUserRole();

        if ($role === RoleType::ADMIN->value || $role === RoleType::PRODUCTION_HOUSE->value) {
            $this->showAdminDashboard($params);
            return;
        }

        if ($role === RoleType::FREELANCER->value) {
            $this->showFreelancerDashboard($params);
            return;
        }

        $this->denyAndRedirectToSignin();
    }

    /**
     * Render the admin or production house dashboard.
     */
    public function showAdminDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::ADMIN->value, RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminDashboardViewModel::createAdminDefault();

        include __DIR__ . '/../Views/dashboards/adminDashboard.php';
    }

    /**
     * Render the freelancer dashboard.
     */
    public function showFreelancerDashboard(array $params = []): void
    {
        $this->requireRole([RoleType::FREELANCER->value]);

        $viewModel = FreelancerDashboardViewModel::createFreelancerDefault();

        include __DIR__ . '/../Views/dashboards/freelancerDashboard.php';
    }

    /**
     * Render production house gig listing screen.
     */
    public function showProductionHouseGigListing(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigRepository = new GigRepository(Config::pdo());
        $ownerId = (int) ($_SESSION['auth_user_id'] ?? 0);
        $ownerName = (string) ($_SESSION['auth_user_name'] ?? 'Production House');
        $gigs = $ownerId > 0 ? $gigRepository->findByOwnerId($ownerId) : [];

        $viewModel = AdminGigListingViewModel::createForGigs($gigs, $ownerName);

        include __DIR__ . '/../Views/dashboards/admin/gigListing.php';
    }

    /**
     * Render production house gig posting screen.
     */
    public function showProductionHouseGigPosting(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $viewModel = AdminGigPostingViewModel::createDefault();

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    /**
     * Handle production house gig posting submissions.
     */
    public function handleProductionHouseGigPosting(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $input = $this->normalizeGigPostingInput($_POST);
        $errors = $this->validateGigPostingInput($input);
        $errors = array_merge($errors, $this->validateGigImageUpload($_FILES['image'] ?? [], true));

        if (!empty($errors)) {
            $this->renderProductionHouseGigPosting($input, $errors);
            return;
        }

        $imageUrl = $this->saveGigImageUpload($_FILES['image'] ?? []);

        if (!empty($errors) || $imageUrl === null) {
            $this->renderProductionHouseGigPosting($input, $errors ?: ['general' => 'Unable to save the gig picture right now.']);
            return;
        }

        try {
            $gigRepository = new GigRepository(Config::pdo());
            $gigRepository->create([
                'ownerId' => (int) ($_SESSION['auth_user_id'] ?? 0),
                'imageUrl' => $imageUrl,
                'title' => $input['title'],
                'description' => $input['description'],
                'category' => $input['category'],
                'location' => $input['location'],
                'startDate' => $input['startDate'],
                'rateType' => $input['rateType'],
                'payRate' => $input['payRate'],
                'status' => $input['status'],
            ]);
        } catch (Throwable $exception) {
            $this->deleteGigImageFile($imageUrl);
            $this->renderProductionHouseGigPosting($input, [
                'general' => 'Unable to save this gig right now. Please try again later.',
            ]);
            return;
        }

        $_SESSION['gig_success_message'] = 'Gig published successfully.';

        $this->redirect('/dashboard/gigs');
    }

    /**
     * Render production house gig editing screen.
     */
    public function showProductionHouseGigEditing(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            $this->redirect('/dashboard/gigs');
        }

        $gigRepository = new GigRepository(Config::pdo());
        $gig = $gigRepository->findById($gigId);

        if ($gig === null || $gig->getOwnerId() !== (int) ($_SESSION['auth_user_id'] ?? 0)) {
            $this->redirect('/dashboard/gigs');
        }

        $viewModel = AdminGigEditingViewModel::createWithGig($gig);

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
    }

    /**
     * Handle production house gig editing submissions.
     */
    public function handleProductionHouseGigEditing(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            $this->redirect('/dashboard/gigs');
        }

        $gigRepository = new GigRepository(Config::pdo());
        $existingGig = $gigRepository->findById($gigId);

        if ($existingGig === null || $existingGig->getOwnerId() !== (int) ($_SESSION['auth_user_id'] ?? 0)) {
            $this->redirect('/dashboard/gigs');
        }

        $input = $this->normalizeGigPostingInput($_POST);
        $errors = $this->validateGigPostingInput($input);
        $errors = array_merge($errors, $this->validateGigImageUpload($_FILES['image'] ?? [], false));

        if (!empty($errors)) {
            $this->renderProductionHouseGigEditing($existingGig, $input, $errors);
            return;
        }

        $previousImageUrl = $existingGig->getImageUrl();
        $imageUrl = $this->saveGigImageUpload($_FILES['image'] ?? [], $previousImageUrl);

        if (!empty($errors) || $imageUrl === null) {
            $this->renderProductionHouseGigEditing($existingGig, $input, $errors ?: ['general' => 'Unable to save the gig picture right now.']);
            return;
        }

        try {
            $gigRepository->update($gigId, [
                'ownerId' => (int) ($_SESSION['auth_user_id'] ?? 0),
                'imageUrl' => $imageUrl,
                'title' => $input['title'],
                'description' => $input['description'],
                'category' => $input['category'],
                'location' => $input['location'],
                'startDate' => $input['startDate'],
                'rateType' => $input['rateType'],
                'payRate' => $input['payRate'],
                'status' => $input['status'],
            ]);
        } catch (Throwable $exception) {
            if ($imageUrl !== $previousImageUrl) {
                $this->deleteGigImageFile($imageUrl);
            }

            $this->renderProductionHouseGigEditing($existingGig, $input, [
                'general' => 'Unable to update this gig right now. Please try again later.',
            ]);
            return;
        }

        if ($imageUrl !== $previousImageUrl) {
            $this->deleteGigImageFile($previousImageUrl);
        }

        $_SESSION['gig_success_message'] = 'Gig updated successfully.';

        $this->redirect('/dashboard/gigs');
    }

    /**
     * Handle production house gig deletion submissions.
     */
    public function handleProductionHouseGigDeletion(array $params = []): void
    {
        $this->requireRole([RoleType::PRODUCTION_HOUSE->value]);

        $gigId = (int) ($params['id'] ?? 0);

        if ($gigId <= 0) {
            $this->redirect('/dashboard/gigs');
        }

        $gigRepository = new GigRepository(Config::pdo());
        $existingGig = $gigRepository->findById($gigId);

        if ($existingGig === null || $existingGig->getOwnerId() !== (int) ($_SESSION['auth_user_id'] ?? 0)) {
            $this->redirect('/dashboard/gigs');
        }

        try {
            $gigRepository->delete($gigId);
        } catch (Throwable $exception) {
            $this->redirect('/dashboard/gigs');
        }

        $this->deleteGigImageFile($existingGig->getImageUrl());

        $_SESSION['gig_success_message'] = 'Gig deleted successfully.';

        $this->redirect('/dashboard/gigs');
    }

    /**
     * Destroy session and send user back to signin.
     */
    private function denyAndRedirectToSignin(): void
    {
        $this->destroySession();
        $this->redirect('/signin');
    }

    /**
     * Render the production house gig posting form with old input and errors.
     */
    private function renderProductionHouseGigPosting(array $oldInput = [], array $errors = []): void
    {
        $viewModel = AdminGigPostingViewModel::createWithInput($oldInput, $errors);

        include __DIR__ . '/../Views/dashboards/admin/gigPosting.php';
    }

    /**
     * Render the production house gig editing form with existing gig data.
     */
    private function renderProductionHouseGigEditing(Gig $gig, array $oldInput = [], array $errors = []): void
    {
        $viewModel = AdminGigEditingViewModel::createWithGig($gig, $oldInput, $errors);

        include __DIR__ . '/../Views/dashboards/admin/gigEditing.php';
    }

    /**
     * Normalize gig posting input before validation and persistence.
     */
    private function normalizeGigPostingInput(array $input): array
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
     * Validate gig posting input.
     */
    private function validateGigPostingInput(array $input): array
    {
        $errors = [];

        if ($input['title'] === '') {
            $errors['title'] = 'Gig title is required.';
        }

        if ($input['description'] === '') {
            $errors['description'] = 'Description is required.';
        }

        if (!in_array($input['category'], ['Camera', 'Editing', 'Sound', 'Production', 'Animation'], true)) {
            $errors['category'] = 'Please choose a valid category.';
        }

        if ($input['location'] === '') {
            $errors['location'] = 'Location is required.';
        }

        if (!$this->isValidDate($input['startDate'])) {
            $errors['startDate'] = 'Start date must be a valid date in YYYY-MM-DD format.';
        }

        if (!in_array($input['rateType'], ['hourly', 'fixed'], true)) {
            $errors['rateType'] = 'Please choose a valid rate type.';
        }

        if (!is_numeric($input['payRate']) || (float) $input['payRate'] <= 0) {
            $errors['payRate'] = 'Pay rate must be a positive number.';
        }

        if (!in_array($input['status'], ['active', 'closed'], true)) {
            $errors['status'] = 'Please choose a valid gig status.';
        }

        return $errors;
    }

    /**
     * Validate an uploaded gig image before saving.
     */
    private function validateGigImageUpload(array $file, bool $required): array
    {
        $errors = [];

        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            if ($required) {
                $errors['image'] = 'Gig picture is required.';
            }

            return $errors;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_INI_SIZE || ($file['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_FORM_SIZE) {
            $errors['image'] = 'Gig picture must be smaller than 5 MB.';

            return $errors;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            $errors['image'] = 'The gig picture could not be uploaded.';

            return $errors;
        }

        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            $errors['image'] = 'Gig picture must be smaller than 5 MB.';

            return $errors;
        }

        $imageInfo = @getimagesize((string) ($file['tmp_name'] ?? ''));

        if ($imageInfo === false) {
            $errors['image'] = 'Gig picture must be a valid image file.';
            return $errors;
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = (string) ($imageInfo['mime'] ?? '');

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            $errors['image'] = 'Gig picture must be JPG, PNG, GIF, or WEBP.';
        }

        return $errors;
    }

    /**
     * Save an uploaded gig image into the public images directory.
     */
    private function saveGigImageUpload(array $file, ?string $fallbackImageUrl = null): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $fallbackImageUrl;
        }

        $validationErrors = $this->validateGigImageUpload($file, false);

        if (!empty($validationErrors)) {
            return $fallbackImageUrl;
        }

        $imageInfo = getimagesize((string) $file['tmp_name']);
        $extension = $this->imageMimeTypeToExtension((string) ($imageInfo['mime'] ?? ''));

        if ($extension === null) {
            return $fallbackImageUrl;
        }

        $directory = __DIR__ . '/../../public/assets/images';

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return $fallbackImageUrl;
        }

        $fileName = sprintf('gig-upload-%s.%s', bin2hex(random_bytes(8)), $extension);
        $targetPath = $directory . '/' . $fileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $targetPath)) {
            return $fallbackImageUrl;
        }

        return '/assets/images/' . $fileName;
    }

    /**
     * Delete a gig image file from the public images directory.
     */
    private function deleteGigImageFile(?string $imageUrl): void
    {
        if ($imageUrl === null || $imageUrl === '') {
            return;
        }

        if (!str_starts_with($imageUrl, '/assets/images/')) {
            return;
        }

        $imagePath = __DIR__ . '/../../public' . $imageUrl;

        if (is_file($imagePath)) {
            @unlink($imagePath);
        }
    }

    /**
     * Map an image MIME type to a file extension.
     */
    private function imageMimeTypeToExtension(string $mimeType): ?string
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
     * Check whether a date string uses the expected database format.
     */
    private function isValidDate(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }

}
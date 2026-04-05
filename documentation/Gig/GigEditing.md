# Gig Editing

## Purpose
Allow production house users to update existing gig details including optional image replacement, with automatic cleanup of replaced images.

## Access Control
- **Restricted To**: Production House users only (must own the gig)
- **Role Check**: `RoleType::PRODUCTION_HOUSE`
- **URLs**:
  - `/dashboard/gigs/{id}/edit` (GET - Show form)
  - `/dashboard/gigs/{id}/edit` (POST - Handle submission)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showProductionHouseGigEditing()` - Display the form
- `DashboardController::handleProductionHouseGigEditing()` - Process submission
- `DashboardController::getAuthenticatedOwnerId()` (private helper)
- `DashboardController::extractAndValidateGigId()` (private helper)
- `DashboardController::handleGigServiceResultWithGig()` (private helper)
- `DashboardController::renderProductionHouseGigEditing()` (private helper)
- `DashboardController::redirectToGigListingWithSuccess()` (private helper)
- `Controller::requireRole()`
- `Controller::redirect()`

## Service Files Used
- [app/src/Services/GigService.php](../../app/src/Services/GigService.php)
- [app/src/Services/Interfaces/IGigService.php](../../app/src/Services/Interfaces/IGigService.php)
- [app/src/Services/GigImageService.php](../../app/src/Services/GigImageService.php)
- [app/src/Services/Interfaces/IGigImageService.php](../../app/src/Services/Interfaces/IGigImageService.php)

### Methods Used
- `GigService::findByIdAndVerifyOwnership(int $gigId, int $ownerId): ?Gig`
- `GigService::update(int $gigId, int $ownerId, array $input, array $imageFile, ?string $currentImageUrl): array`

## Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)

### Methods Used (via GigService)
- `GigRepository::findById(int $gigId): ?Gig`
- `GigRepository::update(int $gigId, array $data): bool`

## Model Files Used
- [app/src/Models/Gig.php](../../app/src/Models/Gig.php)
- [app/src/ViewModels/AdminGigEditingViewModel.php](../../app/src/ViewModels/AdminGigEditingViewModel.php)

### Methods Used
- `AdminGigEditingViewModel::createWithGig(Gig $gig)`
- `AdminGigEditingViewModel::createWithGig(Gig $gig, array $oldInput, array $errors)`

## View Files Used
- [app/src/Views/dashboards/admin/gigEditing.php](../../app/src/Views/dashboards/admin/gigEditing.php)

## Input Validation
Same as Gig Creation. All fields are validated by `GigService::validateInput()` with the same requirements and error messages.

## Image Replacement Handling
- **Optional Image Upload**: User may upload a new image or leave blank to keep current
- **Cleanup**: If new image is provided and succeeds:
  - New image replaces old image URL in database
  - Old image file is automatically deleted from disk
  - Old image URL is overwritten (no orphaned files)
- **Rollback**: If database update fails:
  - Old image URL remains in database (unchanged)
  - Newly uploaded image is deleted from disk
  - Form is re-rendered with errors

## Ownership Verification
Before allowing edit access:
1. The gig ID is extracted and validated from URL parameters
2. The authenticated owner ID is retrieved from session
3. `GigService::findByIdAndVerifyOwnership()` verifies:
   - Gig exists in database
   - Gig is owned by the authenticated user
4. If verification fails, user is redirected to `/dashboard/gigs`

## Flow (GET)
1. The production house user navigates to `/dashboard/gigs/{id}/edit`.
2. `DashboardController::showProductionHouseGigEditing()` is invoked.
3. The controller verifies the user has the `PRODUCTION_HOUSE` role.
4. The gig ID is extracted and validated from URL parameters.
5. The authenticated owner ID is retrieved from session.
6. `GigService::findByIdAndVerifyOwnership()` is called to:
   - Fetch the gig by ID
   - Verify ownership
   - Return null if gig not found or not owned
7. **If verification fails**:
   - User is redirected to `/dashboard/gigs`
8. **If verification succeeds**:
   - View model is created with current gig data
   - Editing form is rendered with:
     - Current gig image displayed as preview
     - Current form values pre-filled
     - File upload field for new image

## Flow (POST)
1. The production house user submits the gig form from `/dashboard/gigs/{id}/edit`.
2. `DashboardController::handleProductionHouseGigEditing()` is invoked.
3. The controller verifies the user has the `PRODUCTION_HOUSE` role.
4. The gig ID is extracted and validated from URL parameters.
5. The authenticated owner ID is retrieved from session.
6. `GigService::findByIdAndVerifyOwnership()` verifies ownership.
7. **If verification fails**:
   - User is redirected to `/dashboard/gigs`
8. **If verification succeeds**:
   - `GigService::update()` is called with:
     - `gigId`: Extracted from URL
     - `ownerId`: From session
     - `input`: Form POST data
     - `imageFile`: Uploaded image from `$_FILES['image']` (optional)
     - `currentImageUrl`: Current image URL from gig
9. **If validation fails**:
   - Errors are returned by the service
   - Form is re-rendered with errors, old input, and current gig data
10. **If submission succeeds**:
    - Gig is updated in the database with new values
    - If new image was provided:
      - Old image file is deleted from disk
      - New image URL is stored in database
    - Success message is stored in session: "Gig updated successfully."
    - User is redirected to `/dashboard/gigs`
11. **If database update fails**:
    - If new image was provided, it is deleted from disk
    - Old gig data remains unchanged
    - An error message is displayed
    - Form is re-rendered for correction

## Error Handling
- **Authentication**: Not authenticated users are redirected to `/signin`.
- **Authorization**: Users without `PRODUCTION_HOUSE` role are redirected to `/dashboard`.
- **Ownership**: If user does not own the gig, redirect to `/dashboard/gigs`.
- **Invalid Gig ID**: Missing or invalid gig ID redirects to `/dashboard/gigs`.
- **Validation Errors**: Form is re-rendered with field-specific error messages and old input.
- **File Upload Errors**: Clear messages guide the user to fix size or format issues.
- **Database Errors**: Generic message: "Unable to update this gig right now. Please try again later."
- **Image Cleanup**: If database update fails, the newly uploaded image is automatically removed.

## Success Response
- **HTTP Status**: Redirect to `/dashboard/gigs` with 302/303
- **User Feedback**: Success message displayed on the gig listing page
- **Session Data**:
  - `$_SESSION['gig_success_message']` = "Gig updated successfully."
  - Message automatically cleared after display

## Important Notes
- **Preserves Seed Images**: Only files matching `gig-upload-*` pattern are deleted; seed images (e.g., SVG files) are preserved
- **Atomic Operations**: Image deletion happens only after successful database update
- **Rollback Support**: If update fails, both database and filesystem remain in their original state

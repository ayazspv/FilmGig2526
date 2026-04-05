# Gig Deletion

## Purpose
Allow production house users to permanently delete gigs they own, with automatic cleanup of associated images.

## Access Control
- **Restricted To**: Production House users only (must own the gig)
- **Role Check**: `RoleType::PRODUCTION_HOUSE`
- **URL**: `/dashboard/gigs/{id}/delete` (POST request)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::handleProductionHouseGigDeletion()` - Process deletion
- `DashboardController::getAuthenticatedOwnerId()` (private helper)
- `DashboardController::extractAndValidateGigId()` (private helper)
- `DashboardController::redirectToGigListingWithSuccess()` (private helper)
- `Controller::requireRole()`
- `Controller::redirect()`

## Service Files Used
- [app/src/Services/GigService.php](../../app/src/Services/GigService.php)
- [app/src/Services/Interfaces/IGigService.php](../../app/src/Services/Interfaces/IGigService.php)
- [app/src/Services/GigImageService.php](../../app/src/Services/GigImageService.php)
- [app/src/Services/Interfaces/IGigImageService.php](../../app/src/Services/Interfaces/IGigImageService.php)

### Methods Used
- `GigService::delete(int $gigId, int $ownerId): array`

## Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)

### Methods Used (via GigService)
- `GigRepository::findById(int $gigId): ?Gig`
- `GigRepository::delete(int $gigId): bool`

## Model Files Used
- [app/src/Models/Gig.php](../../app/src/Models/Gig.php)

## Ownership Verification
Before allowing deletion:
1. The gig ID is extracted and validated from URL parameters
2. The authenticated owner ID is retrieved from session
3. `GigService::delete()` internally verifies:
   - Gig exists in database
   - Gig is owned by the authenticated user
4. If verification fails, a failure result is returned and no deletion occurs

## Image Cleanup
- **Associated Image**: The gig's image file is deleted from disk after successful database deletion
- **Selective Deletion**: Only uploaded images (matching `gig-upload-*` pattern) are deleted; seed images are preserved
- **Atomic Operations**: Image deletion happens only after successful database deletion
- **No Files Left Behind**: Orphaned image files are automatically cleaned up

## Flow
1. The production house user clicks the delete button on a gig card in the listing view.
2. A POST request is submitted to `/dashboard/gigs/{id}/delete`.
3. `DashboardController::handleProductionHouseGigDeletion()` is invoked.
4. The controller verifies the user has the `PRODUCTION_HOUSE` role.
5. The gig ID is extracted and validated from URL parameters.
6. The authenticated owner ID is retrieved from session.
7. `GigService::delete()` is called with:
   - `gigId`: Extracted from URL
   - `ownerId`: From session
8. **Inside GigService::delete()**:
   - The gig is fetched by ID
   - Ownership is verified (gig must be owned by the authenticated user)
   - **If ownership verification fails**:
     - Failure result is returned: "Gig not found or access denied."
     - No deletion occurs
   - **If ownership verification succeeds**:
     - Gig is deleted from database
     - Associated image file is deleted from disk (if it matches upload pattern)
     - Success result is returned: "Gig deleted successfully."
9. **After GigService::delete() returns**:
   - Success message is stored in session: "Gig deleted successfully."
   - User is redirected to `/dashboard/gigs`

## Error Handling
- **Authentication**: Not authenticated users are redirected to `/signin`.
- **Authorization**: Users without `PRODUCTION_HOUSE` role are redirected to `/dashboard`.
- **Ownership**: If user does not own the gig:
  - No deletion occurs
  - User is silently redirected to `/dashboard/gigs`
  - No error message is shown (for security)
- **Invalid Gig ID**: Missing or invalid gig ID redirects to `/dashboard/gigs`.
- **Database Errors**: If deletion fails:
  - Image is not deleted (rollback)
  - User is redirected to `/dashboard/gigs`
  - Silent redirect (no detailed error for security)

## Success Response
- **HTTP Status**: Redirect to `/dashboard/gigs` with 302/303
- **User Feedback**: Success message displayed on the gig listing page
- **Session Data**:
  - `$_SESSION['gig_success_message']` = "Gig deleted successfully."
  - Message automatically cleared after display
- **Database State**: Gig record is permanently removed
- **Filesystem State**: Associated image file is removed from disk

## Important Notes
- **Permanent Action**: Gig deletion cannot be undone; no soft delete is used
- **Cascade Cleanup**: When a gig is deleted, all associated image files are cleaned up
- **Preserves Seed Images**: Only files matching the `gig-upload-*` pattern are deleted
- **Atomic Operations**: Database and filesystem remain consistent; if database deletion fails, image is not touched
- **Silent Failures**: Unauthorized deletion attempts silently redirect without revealing why

## View Integration
The gig listing view includes a delete form for each gig:
```php
<form method="POST" action="/dashboard/gigs/{id}/delete">
    <button type="submit" class="btn btn-danger">Delete Gig</button>
</form>
```

The delete button should typically include a confirmation dialog to prevent accidental deletion:
```javascript
if (!confirm('Are you sure you want to delete this gig?')) {
    event.preventDefault();
}
```

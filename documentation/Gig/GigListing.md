# Gig Listing (Production House Dashboard)

## Purpose
Display all gigs posted by the authenticated production house with options to create, edit, or delete gigs.

## Access Control
- **Restricted To**: Production House users only
- **Role Check**: `RoleType::PRODUCTION_HOUSE`
- **URL**: `/dashboard/gigs` (GET request)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showProductionHouseGigListing()`
- `Controller::requireRole()`
- `Controller::getAuthenticatedOwnerId()` (private helper)
- `Controller::getAuthenticatedOwnerName()` (private helper)

## Service Files Used
- [app/src/Services/GigService.php](../../app/src/Services/GigService.php)
- [app/src/Services/Interfaces/IGigService.php](../../app/src/Services/Interfaces/IGigService.php)

### Methods Used
- `GigService::findByOwnerId(int $ownerId): array`

## Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)

### Methods Used (via GigService)
- `GigRepository::findByOwnerId(int $ownerId): array`

## Model Files Used
- [app/src/Models/Gig.php](../../app/src/Models/Gig.php)
- [app/src/ViewModels/AdminGigListingViewModel.php](../../app/src/ViewModels/AdminGigListingViewModel.php)

### Methods Used
- `AdminGigListingViewModel::createForGigs(array $gigs, string $ownerName)`

## View Files Used
- [app/src/Views/dashboards/admin/gigListing.php](../../app/src/Views/dashboards/admin/gigListing.php)

## Flow
1. The production house user navigates to `/dashboard/gigs`.
2. `DashboardController::showProductionHouseGigListing()` is invoked.
3. The controller verifies the user has the `PRODUCTION_HOUSE` role.
4. The controller retrieves the authenticated user's ID and name from session.
5. `GigService::findByOwnerId()` fetches all gigs owned by this production house.
6. The gigs are passed to `AdminGigListingViewModel::createForGigs()` for formatting.
7. The listing view is rendered with action buttons:
   - **Create New Gig**: Links to `/dashboard/gigs/create`
   - **Edit Gig**: Links to `/dashboard/gigs/{id}/edit`
   - **Delete Gig**: Submits a POST request to `/dashboard/gigs/{id}/delete`
8. Success messages from creating, updating, or deleting gigs are displayed (stored in `$_SESSION['gig_success_message']`).

## Error Handling
- If the user is not authenticated, they are redirected to `/signin`.
- If the user lacks the required `PRODUCTION_HOUSE` role, they are redirected to `/dashboard`.

## Data Structure
The view receives a view model with:
- `pageTitle`: Page title
- `badgeLabel`: Badge text
- `heroTitle`: Hero section title
- `heroDescription`: Hero section description
- `gigs`: Array of gig data, each containing:
  - `gigId`: Unique identifier
  - `title`: Gig title
  - `description`: Short description
  - `category`: Gig category (Camera, Editing, Sound, Production, Animation)
  - `location`: Work location
  - `startDate`: Start date in YYYY-MM-DD format
  - `rate`: Formatted pay rate (e.g., "EUR 55.00/hr")
  - `status`: Gig status (active, closed)
  - `imageUrl`: URL to the gig image

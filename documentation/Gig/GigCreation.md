# Gig Creation (Posting)

## Purpose
Allow production house users to create and post new gig opportunities with image uploads and detailed information.

## Access Control
- **Restricted To**: Production House users only
- **Role Check**: `RoleType::PRODUCTION_HOUSE`
- **URLs**: 
  - `/dashboard/gigs/create` (GET - Show form)
  - `/dashboard/gigs/create` (POST - Handle submission)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showProductionHouseGigPosting()` - Display the form
- `DashboardController::handleProductionHouseGigPosting()` - Process submission
- `DashboardController::getAuthenticatedOwnerId()` (private helper)
- `DashboardController::handleGigServiceResult()` (private helper)
- `DashboardController::renderProductionHouseGigPosting()` (private helper)
- `DashboardController::redirectToGigListingWithSuccess()` (private helper)
- `Controller::requireRole()`
- `Controller::redirect()`

## Service Files Used
- [app/src/Services/GigService.php](../../app/src/Services/GigService.php)
- [app/src/Services/Interfaces/IGigService.php](../../app/src/Services/Interfaces/IGigService.php)
- [app/src/Services/GigImageService.php](../../app/src/Services/GigImageService.php)
- [app/src/Services/Interfaces/IGigImageService.php](../../app/src/Services/Interfaces/IGigImageService.php)

### Methods Used
- `GigService::create(int $ownerId, array $input, array $imageFile): array`

## Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)

### Methods Used (via GigService)
- `GigRepository::create(array $data): int`

## Model Files Used
- [app/src/Models/Gig.php](../../app/src/Models/Gig.php)
- [app/src/ViewModels/AdminGigPostingViewModel.php](../../app/src/ViewModels/AdminGigPostingViewModel.php)

### Methods Used
- `AdminGigPostingViewModel::createDefault()`
- `AdminGigPostingViewModel::createWithInput(array $input, array $errors)`

## View Files Used
- [app/src/Views/dashboards/admin/gigPosting.php](../../app/src/Views/dashboards/admin/gigPosting.php)

## Input Validation
All inputs are validated by `GigService::validateInput()`:

| Field | Requirements | Error Message |
|-------|--------------|---------------|
| `title` | Not empty | "Gig title is required." |
| `description` | Not empty | "Description is required." |
| `category` | One of: Camera, Editing, Sound, Production, Animation | "Please choose a valid category." |
| `location` | Not empty | "Location is required." |
| `startDate` | Valid YYYY-MM-DD format | "Start date must be a valid date in YYYY-MM-DD format." |
| `rateType` | One of: hourly, fixed | "Please choose a valid rate type." |
| `payRate` | Positive number | "Pay rate must be a positive number." |
| `status` | One of: active, closed | "Please choose a valid gig status." |
| `image` | Valid image, ≤5 MB | "Gig picture is required." / "Gig picture must be smaller than 5 MB." / "Gig picture must be JPG, PNG, GIF, or WEBP." |

## Image Upload Handling
- **Allowed Formats**: JPEG, PNG, GIF, WEBP
- **Max Size**: 5 MB
- **Storage Location**: `app/public/assets/images/`
- **Filename Pattern**: `gig-upload-{random_hex}.{extension}`
- **URL Stored**: `/assets/images/gig-upload-{random_hex}.{extension}`
- **Git Ignore Rule**: Files matching `gig-upload-*` are ignored by git

If upload fails after database insertion is successful, the uploaded file is automatically cleaned up.

## Flow (GET)
1. The production house user navigates to `/dashboard/gigs/create`.
2. `DashboardController::showProductionHouseGigPosting()` is invoked.
3. The controller verifies the user has the `PRODUCTION_HOUSE` role.
4. A default view model is created with empty form fields.
5. The gig posting form is rendered.

## Flow (POST)
1. The production house user submits the gig form from `/dashboard/gigs/create`.
2. `DashboardController::handleProductionHouseGigPosting()` is invoked.
3. The controller verifies the user has the `PRODUCTION_HOUSE` role.
4. The authenticated owner ID is retrieved from session.
5. `GigService::create()` is called with:
   - `ownerId`: From session
   - `input`: Form POST data
   - `imageFile`: Uploaded image from `$_FILES['image']`
6. **If validation fails**:
   - Errors are returned by the service
   - The form is re-rendered with errors and old input values
7. **If submission succeeds**:
   - The gig is inserted into the database
   - The gig image is saved to disk
   - A success message is stored in session: "Gig published successfully."
   - The user is redirected to `/dashboard/gigs`
8. **If database insertion fails**:
   - The uploaded image file is automatically deleted
   - An error message is displayed
   - The form is re-rendered for correction

## Error Handling
- **Authentication**: Not authenticated users are redirected to `/signin`.
- **Authorization**: Users without `PRODUCTION_HOUSE` role are redirected to `/dashboard`.
- **Validation Errors**: Form is re-rendered with field-specific error messages and old input.
- **File Upload Errors**: Clear messages guide the user to fix size or format issues.
- **Database Errors**: Generic message: "Unable to save this gig right now. Please try again later."
- **Image Cleanup**: If database save fails, the newly uploaded image is automatically removed from disk.

## Success Response
- **HTTP Status**: Redirect to `/dashboard/gigs` with 302/303
- **User Feedback**: Success message displayed on the gig listing page
- **Session Data**: 
  - `$_SESSION['gig_success_message']` = "Gig published successfully."
  - Message automatically cleared after display

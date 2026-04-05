# Gig

## Purpose
Show public gig listings and gig detail pages.

## Controller Files Used
- [app/src/Controllers/GigController.php](../../app/src/Controllers/GigController.php)

### Methods Used
- `GigController::showGigListing()`
- `GigController::showGigDetail()`

## Model Files Used
- [app/src/ViewModels/GigListingViewModel.php](../../app/src/ViewModels/GigListingViewModel.php)
- [app/src/ViewModels/GigDetailViewModel.php](../../app/src/ViewModels/GigDetailViewModel.php)

### Methods Used
- `GigListingViewModel::createDefault()`
- `GigDetailViewModel::createDefault()`

## Repository Files Used
- None directly in the current gig controller layer.

## Service Files Used
- None directly in the current gig controller layer.

## Flow
1. The user opens `/gigs` for the list view or `/gigs/{id}` for the detail view.
2. `GigController` creates the matching view model.
3. The controller includes the gig list or gig detail view.
4. The view renders gig content using the data prepared by the view model.

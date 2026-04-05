# Home

## Purpose
Render the public homepage with hero content, how-it-works content, gig previews, and platform benefits.

## Controller Files Used
- [app/src/Controllers/HomeController.php](../../app/src/Controllers/HomeController.php)

### Methods Used
- `HomeController::index()`

## Model Files Used
- [app/src/ViewModels/HomeViewModel.php](../../app/src/ViewModels/HomeViewModel.php)

### Methods Used
- `HomeViewModel::createDefault()`

## Repository Files Used
- None directly.

## Service Files Used
- None directly.

## Flow
1. The user opens `/`.
2. `HomeController::index()` creates a default home view model.
3. The controller extracts the view model sections into local variables.
4. The homepage view renders the hero, how-it-works blocks, gig cards, and reason sections.

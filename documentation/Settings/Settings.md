# Settings

## Purpose
Show a role-specific settings page for authenticated users.

## Controller Files Used
- [app/src/Controllers/SettingsController.php](../../app/src/Controllers/SettingsController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `SettingsController::showSettings()`
- `SettingsController::showAdminSettings()`
- `SettingsController::showFreelancerSettings()`
- `Controller::requireAuthentication()`
- `Controller::requireRole()`
- `Controller::authUserRole()`
- `Controller::redirect()`

## Model Files Used
- [app/src/ViewModels/AdminSettingsViewModel.php](../../app/src/ViewModels/AdminSettingsViewModel.php)
- [app/src/ViewModels/FreelancerSettingsViewModel.php](../../app/src/ViewModels/FreelancerSettingsViewModel.php)

### Methods Used
- `AdminSettingsViewModel::createDefault()`
- `FreelancerSettingsViewModel::createDefault()`

## Repository Files Used
- None directly in the current settings controller layer.

## Service Files Used
- None directly in the current settings controller layer.

## Flow
1. The user opens `/settings`.
2. `SettingsController::showSettings()` checks that the user is signed in.
3. The controller reads the role from session and routes to the correct settings page.
4. Admin and production house users see the admin settings screen.
5. Freelancer users see the freelancer settings screen.
6. If the user is not signed in, they are redirected to `/signin`.

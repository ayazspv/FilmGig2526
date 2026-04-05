# Dashboard

## Purpose
Route authenticated users to the correct dashboard and protect role-specific dashboard pages.

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showDashboard()`
- `DashboardController::showAdminDashboard()`
- `DashboardController::showFreelancerDashboard()`
- `DashboardController::showAdminGigListing()`
- `DashboardController::showAdminGigPosting()`
- `DashboardController::showAdminGigEditing()`
- `Controller::requireAuthentication()`
- `Controller::requireRole()`
- `Controller::authUserRole()`
- `Controller::redirect()`

## Model Files Used
- [app/src/ViewModels/AdminDashboardViewModel.php](../../app/src/ViewModels/AdminDashboardViewModel.php)
- [app/src/ViewModels/FreelancerDashboardViewModel.php](../../app/src/ViewModels/FreelancerDashboardViewModel.php)
- [app/src/ViewModels/AdminGigListingViewModel.php](../../app/src/ViewModels/AdminGigListingViewModel.php)
- [app/src/ViewModels/AdminGigPostingViewModel.php](../../app/src/ViewModels/AdminGigPostingViewModel.php)
- [app/src/ViewModels/AdminGigEditingViewModel.php](../../app/src/ViewModels/AdminGigEditingViewModel.php)

### Methods Used
- `AdminDashboardViewModel::createAdminDefault()`
- `FreelancerDashboardViewModel::createFreelancerDefault()`
- `AdminGigListingViewModel::createDefault()`
- `AdminGigPostingViewModel::createDefault()`
- `AdminGigEditingViewModel::createDefault()`

## Repository Files Used
- None directly in the current dashboard controller layer.

## Service Files Used
- None directly in the current dashboard controller layer.

## Flow
1. The user opens `/dashboard`.
2. `DashboardController::showDashboard()` checks authentication.
3. The controller reads the stored role from session.
4. Admin and production house users are routed to the admin dashboard.
5. Freelancer users are routed to the freelancer dashboard.
6. Role-specific gig management pages are protected with `Controller::requireRole()`.
7. If a user is not authenticated, they are redirected to `/signin`.
8. If a user is authenticated but tries to access the wrong role page, they are redirected to `/dashboard`.

# Dashboard

## Purpose
Route authenticated users to the correct dashboard and populate dashboard sections from live JSON API data.

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showDashboard()`
- `DashboardController::showAdminDashboard()`
- `DashboardController::showFreelancerDashboard()`
- `DashboardController::apiDashboard()`
- `DashboardController::showAdminGigListing()`
- `DashboardController::showAdminGigPosting()`
- `DashboardController::showAdminGigEditing()`
- `DashboardController::showAdminSubmissionReview()`
- `DashboardController::handleAdminSubmissionReview()`
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
- `AdminDashboardViewModel::createFromData()`
- `AdminDashboardViewModel::toArray()`
- `FreelancerDashboardViewModel::createFreelancerDefault()`
- `FreelancerDashboardViewModel::createFromData()`
- `FreelancerDashboardViewModel::toArray()`
- `AdminGigListingViewModel::createDefault()`
- `AdminGigPostingViewModel::createDefault()`
- `AdminGigEditingViewModel::createDefault()`
- `AdminSubmissionReviewViewModel::createFromSubmissions()`

## Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)
- [app/src/Repositories/SubmissionRepository.php](../../app/src/Repositories/SubmissionRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)

## Service Files Used
- [app/src/Services/SubmissionService.php](../../app/src/Services/SubmissionService.php)

## Frontend Files Used
- [app/public/assets/js/dashboard.js](../../app/public/assets/js/dashboard.js)
- [app/src/Views/dashboards/adminDashboard.php](../../app/src/Views/dashboards/adminDashboard.php)
- [app/src/Views/dashboards/freelancerDashboard.php](../../app/src/Views/dashboards/freelancerDashboard.php)

## Flow
1. The user opens `/dashboard`.
2. `DashboardController::showDashboard()` checks authentication and routes the user to the correct shell view.
3. The shell view exposes the `/api/dashboard` endpoint to the browser.
4. `dashboard.js` fetches the JSON payload from `/api/dashboard`.
5. `DashboardController::apiDashboard()` builds the live dashboard payload from repositories and services.
6. `AdminDashboardViewModel::toArray()` or `FreelancerDashboardViewModel::toArray()` serializes the dashboard data.
7. The browser renders the stats, tables, and activity lists from the API payload.
8. Role-specific gig management pages are still protected with `Controller::requireRole()`.
9. The admin submission review page is protected with the admin role check.
10. If a user is not authenticated, they are redirected to `/signin`.
11. If a user is authenticated but tries to access the wrong role page, they are redirected to `/dashboard`.

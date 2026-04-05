# Dashboard

## Purpose
Route authenticated users to the correct dashboard and populate dashboard sections from live JSON API data.

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showDashboard()`
- `DashboardController::showAdminDashboard()` - Routes admin and production house users
- `DashboardController::showFreelancerDashboard()`
- `DashboardController::apiDashboard()`
- `DashboardController::showProductionHouseGigListing()` - Production house gig management
- `DashboardController::showProductionHouseGigPosting()` - Production house gig creation
- `DashboardController::handleProductionHouseGigPosting()`
- `DashboardController::showProductionHouseGigEditing()` - Production house gig editing
- `DashboardController::handleProductionHouseGigEditing()`
- `DashboardController::handleProductionHouseGigDeletion()` - Production house gig deletion
- `DashboardController::showProductionHouseSubmissions()` - Production house submission review
- `DashboardController::handleProductionHouseSubmissionReview()`
- `DashboardController::showFreelancerSubmissions()` - Freelancer applications page
- `DashboardController::handleFreelancerSubmissionWithdrawal()`
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
3. `showAdminDashboard()` is called for admin and production house users; `showFreelancerDashboard()` for freelancers.
4. The shell view exposes the `/api/dashboard` endpoint to the browser.
5. `dashboard.js` fetches the JSON payload from `/api/dashboard`.
6. `DashboardController::apiDashboard()` builds the live dashboard payload from repositories and services.
7. `AdminDashboardViewModel::toArray()` or `FreelancerDashboardViewModel::toArray()` serializes the dashboard data.
8. The browser renders the stats, tables, and activity lists from the API payload.

## Production House Dashboard Features
- **Gig Management**: Create, list, edit, and delete gigs via:
  - `GET /dashboard/gigs` - List all gigs from this production house
  - `GET /dashboard/gigs/create` - Create new gig form
  - `POST /dashboard/gigs/create` - Submit new gig
  - `GET /dashboard/gigs/{id}/edit` - Edit gig form
  - `POST /dashboard/gigs/{id}/edit` - Submit gig changes
  - `POST /dashboard/gigs/{id}/delete` - Delete a gig
- **Submission Review**: Review freelancer applications for owned gigs
  - `GET /dashboard/submissions/received` - View all received submissions
  - `POST /dashboard/submissions/{id}/review` - Accept or reject a submission

## Freelancer Dashboard Features
- **Submission Tracking**: View status of all gig applications
  - `GET /dashboard/submissions` - View all submissions
  - `POST /dashboard/submissions/{id}/withdraw` - Withdraw a pending submission

## Security
9. Role-specific gig management pages are protected with `Controller::requireRole()`.
10. The production house submission review page is protected with the production house role check.
11. If a user is not authenticated, they are redirected to `/signin`.
12. If a user is authenticated but tries to access the wrong role page, they are redirected to `/dashboard`.

## Related Documentation
- [Gig Management](../Gig/GigManagement.md) - Production house gig CRUD operations
- [Production House Submission Review](../Submission/ProductionHouseReview.md) - Review freelancer applications
- [Freelancer Submissions](../Submission/SubmissionsPage.md) - Track and withdraw applications

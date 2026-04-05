# Freelancer Submissions Page

## Purpose
Show a freelancer every application they have made, including current status, submission date, and actions they can still take.

## Access Control
- **Restricted To**: Freelancer users only
- **URL**: `/dashboard/submissions` (GET request)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showFreelancerSubmissions()`
- `DashboardController::handleFreelancerSubmissionWithdrawal()`
- `DashboardController::getSubmissionService()` (private helper)
- `DashboardController::renderFreelancerSubmissions()` (private helper)
- `DashboardController::consumeSubmissionFlashMessage()` (private helper)
- `DashboardController::flashSubmissionResult()` (private helper)

## Service Files Used
- [app/src/Services/SubmissionService.php](../../app/src/Services/SubmissionService.php)

### Methods Used
- `SubmissionService::getFreelancerSubmissions(int $userId): array`
- `SubmissionService::withdrawSubmission(int $submissionId, int $userId): array`

## ViewModel Files Used
- [app/src/ViewModels/FreelancerSubmissionsViewModel.php](../../app/src/ViewModels/FreelancerSubmissionsViewModel.php)

### Methods Used
- `FreelancerSubmissionsViewModel::createFromSubmissions(array $submissions)`

## View Files Used
- [app/src/Views/dashboards/freelancer/submissions.php](../../app/src/Views/dashboards/freelancer/submissions.php)
- [app/src/Views/partials/navbars/freelanceNavbar.php](../../app/src/Views/partials/navbars/freelanceNavbar.php)
- [app/src/Views/dashboards/freelancerDashboard.php](../../app/src/Views/dashboards/freelancerDashboard.php)

## Flow
1. The freelancer opens `/dashboard/submissions`.
2. `DashboardController::showFreelancerSubmissions()` checks the freelancer role.
3. The controller asks `SubmissionService` for the authenticated freelancer’s submissions.
4. The service resolves the freelancer profile from the session user and loads all matching submission rows.
5. The service maps each submission into display-ready data, including gig title, category, location, status, and whether withdrawal is allowed.
6. The view model calculates summary stats such as total, pending, accepted, and rejected submissions.
7. The page renders a table with status badges and action buttons.
8. Pending submissions show a Withdraw button.
9. Non-pending submissions only show the gig view link.

## Withdraw Action
- **URL**: `POST /dashboard/submissions/{id}/withdraw`
- **Allowed When**: Submission is owned by the authenticated freelancer and status is `pending`
- **Result**: The submission row is deleted from the database and the page shows a success flash message

## Page Data
The view receives:
- `stats`: Summary cards for total, pending, accepted, and rejected submissions
- `submissions`: Table rows with gig title, category, location, status, submitted timestamp, and actions
- `successMessage`: Flash success text after withdrawal
- `errorMessage`: Flash error text if withdrawal fails

## Error Handling
- If the user is not authenticated, they are redirected to `/signin`.
- If the user is not a freelancer, they are redirected to `/dashboard`.
- If there are no submissions, the table shows an empty-state message.
- If withdrawal is attempted on a non-pending submission, the service rejects it.

## Navigation
- The freelancer navbar links directly to `/dashboard/submissions`.
- The freelancer dashboard also includes a shortcut button to the submissions page.

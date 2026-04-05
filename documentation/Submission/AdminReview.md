# Admin Submission Review

## Purpose
Let admins review pending freelancer submissions, accept or reject them, and keep gig status aligned with the review decision.

## Access Control
- **Restricted To**: Admin users only
- **URL**: `/dashboard/submissions/review` (GET request)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showAdminSubmissionReview()`
- `DashboardController::handleAdminSubmissionReview()`
- `DashboardController::getSubmissionService()` (private helper)
- `DashboardController::renderAdminSubmissionReview()` (private helper)
- `DashboardController::consumeSubmissionFlashMessage()` (private helper)
- `DashboardController::flashSubmissionResult()` (private helper)

## Service Files Used
- [app/src/Services/SubmissionService.php](../../app/src/Services/SubmissionService.php)

### Methods Used
- `SubmissionService::getAdminReviewSubmissions(): array`
- `SubmissionService::reviewSubmission(int $submissionId, string $decision): array`

## ViewModel Files Used
- [app/src/ViewModels/AdminSubmissionReviewViewModel.php](../../app/src/ViewModels/AdminSubmissionReviewViewModel.php)

### Methods Used
- `AdminSubmissionReviewViewModel::createFromSubmissions(array $submissions)`

## View Files Used
- [app/src/Views/dashboards/admin/submissions.php](../../app/src/Views/dashboards/admin/submissions.php)
- [app/src/Views/partials/navbars/adminNavbar.php](../../app/src/Views/partials/navbars/adminNavbar.php)
- [app/src/Views/dashboards/adminDashboard.php](../../app/src/Views/dashboards/adminDashboard.php)

## Flow
1. An admin opens `/dashboard/submissions/review`.
2. `DashboardController::showAdminSubmissionReview()` checks that the current user has the admin role.
3. The controller asks `SubmissionService` for all submissions that can be reviewed.
4. The service loads submission rows together with gig and freelancer data, then maps them into display-ready records.
5. The view model calculates summary stats for total, pending, accepted, and rejected submissions.
6. The page renders a review table with the gig, freelancer, status, and action buttons.
7. Pending submissions show Accept and Reject actions.
8. Non-pending submissions stay visible for history but cannot be reviewed again.

## Review Action
- **URL**: `POST /dashboard/submissions/{id}/review`
- **Allowed When**: Submission belongs to a gig that still has a reviewable state and the current submission is `pending`
- **Result**: The submission status is updated, and the related gig status is synchronized with the decision

## Review Rules
- Only admins can access the review page or submit a review decision.
- Only `pending` submissions can be reviewed.
- Accepting a submission marks it as `accepted`, closes the gig, and rejects the remaining pending submissions for that gig.
- Rejecting a submission marks it as `rejected`.
- If no pending submissions remain after a rejection, the gig is closed.

## Page Data
The view receives:
- `stats`: Summary cards for total, pending, accepted, and rejected submissions
- `submissions`: Table rows with gig title, gig status, freelancer details, submission status, and review actions
- `successMessage`: Flash success text after a review decision
- `errorMessage`: Flash error text if the review fails

## Error Handling
- If the user is not authenticated, they are redirected to `/signin`.
- If the user is not an admin, they are redirected to `/dashboard`.
- If the submission is not pending, the service rejects the review.
- If the review decision is invalid, the service returns a friendly error message.

## Navigation
- The admin navbar links directly to `/dashboard/submissions/review`.
- The admin dashboard includes a shortcut button to the review page.
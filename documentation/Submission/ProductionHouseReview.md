# Production House Submission Review

## Purpose
Let production house users review freelancer submissions for their own gigs, accept or reject them, and keep gig status aligned with the review decision.

## Access Control
- **Restricted To**: Production House users only
- **URL**: `/dashboard/submissions/received` (GET request)

## Controller Files Used
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `DashboardController::showProductionHouseSubmissions()`
- `DashboardController::handleProductionHouseSubmissionReview()`
- `DashboardController::getSubmissionService()` (private helper)
- `DashboardController::renderProductionHouseSubmissions()` (private helper)
- `DashboardController::consumeSubmissionFlashMessage()` (private helper)
- `DashboardController::flashProductionHouseSubmissionReviewResult()` (private helper)

## Service Files Used
- [app/src/Services/SubmissionService.php](../../app/src/Services/SubmissionService.php)

### Methods Used
- `SubmissionService::getProductionHouseSubmissions(int $productionHouseOwnerId): array`
- `SubmissionService::reviewSubmission(int $submissionId, int $ownerId, string $decision): array`

## ViewModel Files Used
- [app/src/ViewModels/AdminSubmissionReviewViewModel.php](../../app/src/ViewModels/AdminSubmissionReviewViewModel.php)

### Methods Used
- `AdminSubmissionReviewViewModel::createFromSubmissions(array $submissions)`

## View Files Used
- [app/src/Views/dashboards/admin/submissions.php](../../app/src/Views/dashboards/admin/submissions.php)
- [app/src/Views/partials/navbars/adminNavbar.php](../../app/src/Views/partials/navbars/adminNavbar.php)
- [app/src/Views/dashboards/adminDashboard.php](../../app/src/Views/dashboards/adminDashboard.php)

## Flow
1. A production house user opens `/dashboard/submissions/received`.
2. `DashboardController::showProductionHouseSubmissions()` checks that the current user has the production house role.
3. The controller retrieves their authenticated owner ID from session.
4. The controller asks `SubmissionService` for submissions received for gigs owned by this production house.
5. The service loads submission rows together with gig and freelancer data, then maps them into display-ready records.
6. The view model calculates summary stats for total, pending, accepted, and rejected submissions.
7. The page renders a review table with the gig, freelancer, status, and action buttons.
8. Pending submissions show Accept and Reject actions.
9. Non-pending submissions stay visible for history but cannot be reviewed again.

## Review Action
- **URL**: `POST /dashboard/submissions/{id}/review`
- **Allowed When**: Submission belongs to a gig owned by the authenticated production house and the current submission is `pending`
- **Result**: The submission status is updated, and the related gig status is synchronized with the decision

## Review Rules
- Only production house users can access the submissions received page or submit a review decision.
- A production house can only review submissions for gigs they own.
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
- If the user is not a production house, they are redirected to `/dashboard`.
- If the submission belongs to a gig the user does not own, the review is rejected.
- If the submission is not pending, the service rejects the review.
- If the review decision is invalid, the service returns a friendly error message.

## Navigation
- The production house navbar links directly to `/dashboard/submissions/received`.
- The production house dashboard includes a shortcut button to the submissions received page.

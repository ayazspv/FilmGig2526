# Submission

## Purpose
Let freelancers apply to gigs, track the status of their submissions, and withdraw pending applications before a production house acts on them.

## Access Control
- **Applying to a gig**: Freelancer users only
- **Viewing submissions**: Freelancer users only
- **Withdrawing a submission**: Freelancer users only, and only while the submission is `pending`

## Controller Files Used
- [app/src/Controllers/GigController.php](../../app/src/Controllers/GigController.php)
- [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `GigController::showGigDetail()`
- `GigController::handleGigApplication()`
- `DashboardController::showFreelancerSubmissions()`
- `DashboardController::handleFreelancerSubmissionWithdrawal()`
- `Controller::requireRole()`
- `Controller::redirect()`

## Service Files Used
- [app/src/Services/SubmissionService.php](../../app/src/Services/SubmissionService.php)
- [app/src/Services/Interfaces/ISubmissionService.php](../../app/src/Services/Interfaces/ISubmissionService.php)

### Methods Used
- `SubmissionService::applyToGig(int $gigId, int $userId): array`
- `SubmissionService::getFreelancerSubmissions(int $userId): array`
- `SubmissionService::withdrawSubmission(int $submissionId, int $userId): array`

## Repository Files Used
- [app/src/Repositories/SubmissionRepository.php](../../app/src/Repositories/SubmissionRepository.php)
- [app/src/Repositories/Interfaces/ISubmissionRepository.php](../../app/src/Repositories/Interfaces/ISubmissionRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)

### Methods Used
- `SubmissionRepository::create(array $data): int`
- `SubmissionRepository::findById(int $submissionId): ?Submission`
- `SubmissionRepository::findByGigId(int $gigId): array`
- `SubmissionRepository::findByGigIdAndFreelancerId(int $gigId, int $freelancerId): ?Submission`
- `SubmissionRepository::findByFreelancerId(int $freelancerId): array`
- `SubmissionRepository::delete(int $submissionId): bool`
- `FreelancerRepository::findByUserId(int $userId): ?Freelancer`
- `GigRepository::findById(int $gigId): ?Gig`

## Model Files Used
- [app/src/Models/Submission.php](../../app/src/Models/Submission.php)
- [app/src/Models/Freelancer.php](../../app/src/Models/Freelancer.php)
- [app/src/Models/Gig.php](../../app/src/Models/Gig.php)
- [app/src/Enums/SubmissionStatus.php](../../app/src/Enums/SubmissionStatus.php)

### Methods Used
- `Submission::getSubmissionId()`
- `Submission::getGigId()`
- `Submission::getFreelancerId()`
- `Submission::getStatus()`
- `Submission::getSubmittedAt()`
- `SubmissionStatus::isValid()`

## ViewModel Files Used
- [app/src/ViewModels/GigDetailViewModel.php](../../app/src/ViewModels/GigDetailViewModel.php)
- [app/src/ViewModels/FreelancerSubmissionsViewModel.php](../../app/src/ViewModels/FreelancerSubmissionsViewModel.php)

### Methods Used
- `GigDetailViewModel::createFromGig()`
- `FreelancerSubmissionsViewModel::createFromSubmissions()`

## View Files Used
- [app/src/Views/gigs/gigDetail.php](../../app/src/Views/gigs/gigDetail.php)
- [app/src/Views/dashboards/freelancer/submissions.php](../../app/src/Views/dashboards/freelancer/submissions.php)
- [app/src/Views/dashboards/freelancerDashboard.php](../../app/src/Views/dashboards/freelancerDashboard.php)
- [app/src/Views/partials/navbars/freelanceNavbar.php](../../app/src/Views/partials/navbars/freelanceNavbar.php)

## Flow
1. A freelancer opens a gig detail page at `/gigs/{id}`.
2. `GigController::showGigDetail()` loads the gig and passes it to the detail view model.
3. The controller checks whether the authenticated freelancer already has a submission for that gig.
4. If not applied yet, the page shows an **Apply for This Gig** button.
5. If already applied, the page shows **You have already applied for this gig** and a link to the submissions page.
6. When the freelancer submits the apply form, `GigController::handleGigApplication()` calls `SubmissionService::applyToGig()`.
7. The service validates the gig, the freelancer profile, and duplicate submissions.
8. On success, the service creates a `pending` submission.
9. The freelancer can open `/dashboard/submissions` to review status history.
10. Pending submissions can be withdrawn from the submissions page.

## URLs
- `POST /gigs/{id}/apply` - Apply to a gig
- `GET /dashboard/submissions` - View all submission records for the authenticated freelancer
- `POST /dashboard/submissions/{id}/withdraw` - Withdraw a pending submission

## Submission Rules
- A freelancer can only apply if they are authenticated and have a freelancer profile.
- A freelancer cannot apply to their own gig.
- Duplicate applications for the same gig are rejected.
- New applications are created with `pending` status.
- Only `pending` submissions can be withdrawn.
- Accepted or rejected submissions remain visible but cannot be withdrawn.

## Error Handling
- **Authentication**: Unauthenticated users are redirected to `/signin`.
- **Authorization**: Non-freelancer users are redirected to `/dashboard`.
- **Duplicate applications**: The apply CTA changes to an already-applied state and the service rejects repeat submissions.
- **Missing freelancer profile**: The service returns a friendly error message.
- **Withdraw failures**: The service blocks non-pending or mismatched submissions.

## Database Behavior
- Submissions are stored in the `submission` table.
- Each submission references a gig and a freelancer.
- The initial status is `pending`.
- Withdrawn submissions are deleted, so the record disappears from the freelancer submission list.

# Profile

## Purpose
Show a role-specific profile page for authenticated users.

## Controller Files Used
- [app/src/Controllers/ProfileController.php](../../app/src/Controllers/ProfileController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `ProfileController::showProfile()`
- `ProfileController::showAdminProfile()`
- `ProfileController::showFreelancerProfile()`
- `Controller::requireAuthentication()`
- `Controller::requireRole()`
- `Controller::authUserRole()`
- `Controller::redirect()`

## Model Files Used
- [app/src/ViewModels/AdminProfileViewModel.php](../../app/src/ViewModels/AdminProfileViewModel.php)
- [app/src/ViewModels/FreelancerProfileViewModel.php](../../app/src/ViewModels/FreelancerProfileViewModel.php)

### Methods Used
- `AdminProfileViewModel::createDefault()`
- `FreelancerProfileViewModel::createDefault()`

## Repository Files Used
- None directly in the current profile controller layer.

## Service Files Used
- None directly in the current profile controller layer.

## Flow
1. The user opens `/profile`.
2. `ProfileController::showProfile()` checks that the user is signed in.
3. The controller reads the role from session and routes to the correct profile page.
4. Admin and production house users see the admin profile.
5. Freelancer users see the freelancer profile.
6. If the user is not signed in, they are redirected to `/signin`.

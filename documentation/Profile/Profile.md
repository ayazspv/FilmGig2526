# Profile

## Purpose
Show role-specific profile pages and allow authenticated users to update profile information and profile pictures.

## Quick Links
- [Profile Editing](ProfileEditing.md) - End-to-end update flow for freelancer and production house users
- [Public Profiles](PublicProfiles.md) - Public read-only profiles for production houses and freelancers

## Controller Files Used
- [app/src/Controllers/ProfileController.php](../../app/src/Controllers/ProfileController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `ProfileController::showProfile()`
- `ProfileController::showAdminProfile()`
- `ProfileController::showFreelancerProfile()`
- `ProfileController::showProductionHousePublicProfile()` - View public production house profile
- `ProfileController::showFreelancerPublicProfile()` - View public freelancer profile
- `ProfileController::handleProfileUpdate()`
- `ProfileController::handleAdminProfileUpdate()`
- `ProfileController::handleFreelancerProfileUpdate()`
- `Controller::requireAuthentication()`
- `Controller::requireRole()`
- `Controller::authUserRole()`
- `Controller::redirect()`

## Model Files Used
- [app/src/ViewModels/AdminProfileViewModel.php](../../app/src/ViewModels/AdminProfileViewModel.php)
- [app/src/ViewModels/FreelancerProfileViewModel.php](../../app/src/ViewModels/FreelancerProfileViewModel.php)

### Methods Used
- `AdminProfileViewModel::createFromData()`
- `FreelancerProfileViewModel::createFromData()`

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)

## Service Files Used
- [app/src/Services/ProfileService.php](../../app/src/Services/ProfileService.php)
- [app/src/Services/Interfaces/IProfileService.php](../../app/src/Services/Interfaces/IProfileService.php)

### Methods Used
- `ProfileService::getAdminProfileData(int $userId): array`
- `ProfileService::getFreelancerProfileData(int $userId): array`
- `ProfileService::updateAdminProfile(int $userId, array $input, array $profileImageFile): array`
- `ProfileService::updateFreelancerProfile(int $userId, array $input, array $profileImageFile): array`

## View Files Used
- [app/src/Views/profiles/adminProfile.php](../../app/src/Views/profiles/adminProfile.php)
- [app/src/Views/profiles/freelancerProfile.php](../../app/src/Views/profiles/freelancerProfile.php)

## Flow
1. The user opens `/profile`.
2. `ProfileController::showProfile()` checks that the user is signed in.
3. The controller reads the role from session and routes to the correct profile page.
4. The controller asks `ProfileService` for role-specific profile form data.
5. The matching view model prepares the profile form, validation errors, and success message.
6. The user submits updates to `POST /profile`, including optional `profileImage` upload.
7. `ProfileService` validates input, updates role-specific records, and processes profile image upload.
8. On success, the user is redirected back to `/profile` with a success flash message.
9. On validation failure, the user is redirected back to `/profile` with inline field errors.
10. If the user is not signed in, they are redirected to `/signin`.

## URLs
- `GET /profile` - Show role-specific profile page
- `POST /profile` - Save profile changes for authenticated user

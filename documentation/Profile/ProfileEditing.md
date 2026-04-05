# Profile Editing

## Purpose
Allow freelancer and production house users to update their profile details and upload a new profile picture from the profile page.

## Access Control
- **Restricted To**: Authenticated users only
- **Role Routing**:
  - Production house and admin users use the company-profile form shape
  - Freelancer users use the freelancer-profile form shape

## Controller Files Used
- [app/src/Controllers/ProfileController.php](../../app/src/Controllers/ProfileController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `ProfileController::showProfile()`
- `ProfileController::showAdminProfile()`
- `ProfileController::showFreelancerProfile()`
- `ProfileController::handleProfileUpdate()`
- `ProfileController::handleAdminProfileUpdate()`
- `ProfileController::handleFreelancerProfileUpdate()`
- `ProfileController::handleProfileUpdateResult()`

## Service Files Used
- [app/src/Services/ProfileService.php](../../app/src/Services/ProfileService.php)
- [app/src/Services/Interfaces/IProfileService.php](../../app/src/Services/Interfaces/IProfileService.php)

### Methods Used
- `ProfileService::getAdminProfileData(int $userId): array`
- `ProfileService::getFreelancerProfileData(int $userId): array`
- `ProfileService::updateAdminProfile(int $userId, array $input, array $profileImageFile): array`
- `ProfileService::updateFreelancerProfile(int $userId, array $input, array $profileImageFile): array`

### Refactor Notes
- Public update methods are now short orchestration methods.
- Validation, persistence, and completion responsibilities are split into focused private helpers.
- Main helper groups include:
  - user resolution (`resolveUserForUpdate`)
  - update validation (`validateAdminProfileUpdate`, `validateFreelancerProfileUpdate`)
  - transactional persistence (`persistAdminProfileUpdate`, `persistFreelancerProfileUpdate`)
  - finalization (`completeProfileUpdate`)
- This keeps business behavior unchanged while improving readability and maintainability.

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)

## ViewModel Files Used
- [app/src/ViewModels/AdminProfileViewModel.php](../../app/src/ViewModels/AdminProfileViewModel.php)
- [app/src/ViewModels/FreelancerProfileViewModel.php](../../app/src/ViewModels/FreelancerProfileViewModel.php)

### Methods Used
- `AdminProfileViewModel::createFromData()`
- `FreelancerProfileViewModel::createFromData()`

## View Files Used
- [app/src/Views/profiles/adminProfile.php](../../app/src/Views/profiles/adminProfile.php)
- [app/src/Views/profiles/freelancerProfile.php](../../app/src/Views/profiles/freelancerProfile.php)

## Flow
1. User opens `GET /profile`.
2. Role is checked and the matching profile page is selected.
3. Service returns current profile values and existing profile image URL.
4. User edits fields and optionally selects a new image.
5. Form submits to `POST /profile` with `multipart/form-data`.
6. Service validates text fields and profile image constraints.
7. If valid, user/freelancer/production-house records are updated in a transaction.
8. If an image was uploaded, the old local profile image is replaced with the new one.
9. User is redirected back to `/profile` with success or error flash feedback.

## Validation Rules
- Username: required, unique across users
- Email: required, valid format, unique across users
- Address: required
- Freelancer full name: required
- Production house contact name: required
- Production house company name: required
- Website (production house): optional, but must be a valid URL when provided
- Date of birth (freelancer): optional, but must match `YYYY-MM-DD` if provided

## Profile Image Rules
- Allowed types: JPG, PNG, GIF, WEBP
- Max size: 5 MB
- Storage: local filesystem under `app/public/assets/images`
- Naming convention: `profile-user-{userId}.{ext}`
- Replacement behavior: existing image for the same user is removed before saving a new one

## URLs
- `GET /profile` - Show the profile form for the current user role
- `POST /profile` - Save profile changes
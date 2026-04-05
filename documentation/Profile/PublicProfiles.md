# Public Profiles

## Purpose
Allow all users to view public profile pages for production houses and freelancers, showcasing their key information and portfolios.

## Access Control
- **Viewing**: Public access (no authentication required)
- **URLs**: 
  - `/profiles/productionhouse/{id}` - View production house public profile
  - `/profiles/freelancer/{id}` - View freelancer public profile

## Controller Files Used
- [app/src/Controllers/ProfileController.php](../../app/src/Controllers/ProfileController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `ProfileController::showProductionHousePublicProfile()`
- `ProfileController::showFreelancerPublicProfile()`
- `ProfileController::extractPublicProfileUserId()` (private helper)

## Service Files Used
- [app/src/Services/ProfileService.php](../../app/src/Services/ProfileService.php)
- [app/src/Services/Interfaces/IProfileService.php](../../app/src/Services/Interfaces/IProfileService.php)

### Methods Used
- `ProfileService::getProductionHousePublicProfileData(int $userId): ?array`
- `ProfileService::getFreelancerPublicProfileData(int $userId): ?array`

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)

## View Files Used
- [app/src/Views/profiles/productionHousePublicProfile.php](../../app/src/Views/profiles/productionHousePublicProfile.php)
- [app/src/Views/profiles/freelancerPublicProfile.php](../../app/src/Views/profiles/freelancerPublicProfile.php)

## Production House Public Profile Flow
1. User navigates to `/profiles/productionhouse/{id}` (or accesses link from gig detail page).
2. `ProfileController::showProductionHousePublicProfile()` is invoked.
3. The controller extracts and validates the user ID from URL parameters.
4. `ProfileService::getProductionHousePublicProfileData()` is called to fetch:
   - Production house name
   - Description
   - Contact information
   - Logo/profile image
   - List of active gigs posted by this production house
5. **If production house is not found**:
   - User is redirected to `/gigs`
6. **If production house is found**:
   - Public profile page is rendered showing all displayed information
   - Users can view active gigs and navigate to gig details

## Freelancer Public Profile Flow
1. User navigates to `/profiles/freelancer/{id}` (or accesses link from gig application/review context).
2. `ProfileController::showFreelancerPublicProfile()` is invoked.
3. The controller extracts and validates the user ID from URL parameters.
4. `ProfileService::getFreelancerPublicProfileData()` is called to fetch:
   - Freelancer name
   - Portfolio description
   - Contact information
   - Profile image
   - Portfolio highlights
5. **If freelancer is not found**:
   - User is redirected to `/gigs`
6. **If freelancer is found**:
   - Public profile page is rendered showing portfolio and professional information

## Data Structure
### Production House Public Profile
- `name`: Production house name
- `description`: Company description
- `categoryCount`: Number of gig categories posted
- `gigs`: Array of active gigs with basic details

### Freelancer Public Profile
- `name`: Freelancer full name
- `description`: Portfolio description
- `specializations`: Skill categories and specializations
- `image`: Profile image URL

## Error Handling
- **Invalid User ID**: Invalid or missing user ID in URL redirects to `/gigs`
- **Not Found**: User does not exist redirects to `/gigs`
- **Deactivated Accounts**: Attempting to view profiles of deactivated users redirects to `/gigs`

## Navigation
- Production house names in gig listing pages may link to their public profiles
- Freelancer names in submission review pages may link to their public profiles
- Home page may include featured production houses or freelancers with links to their public profiles

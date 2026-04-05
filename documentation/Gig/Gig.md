# Gig Management

## Overview
The FilmGig2526 application has two distinct gig-related areas:

1. **Gig Management** (Production House Dashboard)
	- Create, read, update, and delete gigs
	- Upload and manage gig images
	- Role-restricted to production house users

2. **Public Gig Browsing** (Public Pages)
	- View public gig listings
	- View detailed gig information
	- Apply for gigs (freelancers only)

## Production House Gig Management

### Quick Links
- **Overview**: [GigManagement.md](GigManagement.md) - System architecture and design
- **List Gigs**: [GigListing.md](GigListing.md) - View production house gigs
- **Create Gig**: [GigCreation.md](GigCreation.md) - Post new gigs
- **Edit Gig**: [GigEditing.md](GigEditing.md) - Update gig details and images
- **Delete Gig**: [GigDeletion.md](GigDeletion.md) - Remove gigs

### Key Features
- ✅ Image upload with validation (JPG, PNG, GIF, WEBP, max 5MB)
- ✅ Automatic image cleanup on replacement or deletion
- ✅ Full input validation with user-friendly error messages
- ✅ Ownership verification for all operations
- ✅ Database and filesystem transaction safety
- ✅ Service layer abstraction for business logic
- ✅ Role-based access control

## Public Gig Browsing

### Purpose
Show public gig listings and detailed gig information to all users. Freelancers can apply for gigs.

### Controller Files Used
- [app/src/Controllers/GigController.php](../../app/src/Controllers/GigController.php)

### Methods Used
- `GigController::showGigListing()` - Display all active gigs
- `GigController::showGigDetail()` - Display single gig details

### Model Files Used
- [app/src/ViewModels/GigListingViewModel.php](../../app/src/ViewModels/GigListingViewModel.php)
- [app/src/ViewModels/GigDetailViewModel.php](../../app/src/ViewModels/GigDetailViewModel.php)

### Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)

### Flow
1. The user opens `/gigs` for the list view or `/gigs/{id}` for the detail view
2. `GigController` queries the repositories for gig data
3. The controller creates matching view model with normalized data
4. The controller includes the appropriate view
5. The view renders gig content using the prepared data

### URLs
- `GET /gigs` - Browse all active gigs (paginated)
- `GET /gigs/{id}` - View detailed information for a specific gig

### Access Control
- Public access (no authentication required)
- Viewing: All users
- Applying for gigs: Freelancers only (CTA hidden for other roles)

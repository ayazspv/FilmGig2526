# Gig Management System

## Overview
The Gig Management System allows production houses to create, read, update, and delete gigs they own, complete with image uploads, validation, and automatic cleanup. The system is built using service layer abstraction for proper separation of concerns and follows object-oriented principles throughout.

## Architecture

### Layered Design
```
Controllers (Request Routing & Authorization)
    ↓
Services (Business Logic & Validation)
    ↓ (GigImageService, GigService)
    ↓
Repositories (Data Persistence)
    ↓ (GigRepository)
    ↓
Models (Domain Objects)
    ↓ (Gig, ViewModels)
    ↓
Views (Presentation)
```

## Core Components

### 1. Controllers
- **File**: [app/src/Controllers/DashboardController.php](../../app/src/Controllers/DashboardController.php)
- **Responsibilities**:
  - Route requests based on HTTP method and URL
  - Enforce authentication and role-based authorization
  - Extract HTTP parameters and session data
  - Delegate business logic to services
  - Render views with prepared data
- **Key Methods**:
  - `showProductionHouseGigListing()` - Display list of production house's gigs
  - `showProductionHouseGigPosting()` - Display create gig form
  - `handleProductionHouseGigPosting()` - Process create gig submission
  - `showProductionHouseGigEditing()` - Display edit gig form
  - `handleProductionHouseGigEditing()` - Process edit gig submission
  - `handleProductionHouseGigDeletion()` - Process delete gig request

### 2. Services

#### GigService
- **File**: [app/src/Services/GigService.php](../../app/src/Services/GigService.php)
- **Interface**: [app/src/Services/Interfaces/IGigService.php](../../app/src/Services/Interfaces/IGigService.php)
- **Responsibilities**:
  - Create gigs with validation and image upload
  - Update gigs with optional image replacement
  - Delete gigs with image cleanup
  - Fetch gigs with ownership verification
  - Validate all input data
  - Handle transactional logic
- **Key Public Methods**:
  - `create(int $ownerId, array $input, array $imageFile): array`
  - `update(int $gigId, int $ownerId, array $input, array $imageFile, ?string $currentImageUrl): array`
  - `delete(int $gigId, int $ownerId): array`
  - `findByOwnerId(int $ownerId): array`
  - `findByIdAndVerifyOwnership(int $gigId, int $ownerId): ?Gig`

#### GigImageService
- **File**: [app/src/Services/GigImageService.php](../../app/src/Services/GigImageService.php)
- **Interface**: [app/src/Services/Interfaces/IGigImageService.php](../../app/src/Services/Interfaces/IGigImageService.php)
- **Responsibilities**:
  - Validate uploaded image files
  - Save uploaded images to disk
  - Delete image files from disk
  - Convert MIME types to extensions
- **Key Public Methods**:
  - `validateUpload(array $file, bool $required = true): array`
  - `save(array $file, ?string $fallbackImageUrl = null): ?string`
  - `delete(?string $imageUrl): bool`

### 3. Repositories
- **File**: [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)
- **Responsibilities**:
  - Persist gig data to database
  - Query gigs by various criteria
  - Map database rows to domain models
- **Methods Used by Services**:
  - `create(array $data): int` - Insert new gig
  - `update(int $gigId, array $data): bool` - Update existing gig
  - `delete(int $gigId): bool` - Delete gig
  - `findById(int $gigId): ?Gig` - Fetch gig by ID
  - `findByOwnerId(int $ownerId): array` - Fetch all gigs owned by user

### 4. Models
- **Domain**: [app/src/Models/Gig.php](../../app/src/Models/Gig.php)
  - Represents a single gig record with getters/setters
  - Properties: gigId, ownerId, imageUrl, title, description, category, location, startDate, rateType, payRate, status, createdAt
  
- **View Models**:
  - [app/src/ViewModels/AdminGigListingViewModel.php](../../app/src/ViewModels/AdminGigListingViewModel.php) - Formats gigs for list display
  - [app/src/ViewModels/AdminGigPostingViewModel.php](../../app/src/ViewModels/AdminGigPostingViewModel.php) - Prepares data for create form
  - [app/src/ViewModels/AdminGigEditingViewModel.php](../../app/src/ViewModels/AdminGigEditingViewModel.php) - Prepares data for edit form

### 5. Views
- [app/src/Views/dashboards/admin/gigListing.php](../../app/src/Views/dashboards/admin/gigListing.php) - List gigs owned by production house
- [app/src/Views/dashboards/admin/gigPosting.php](../../app/src/Views/dashboards/admin/gigPosting.php) - Create gig form
- [app/src/Views/dashboards/admin/gigEditing.php](../../app/src/Views/dashboards/admin/gigEditing.php) - Edit gig form

## Data Model

### Gig Table Schema
```sql
CREATE TABLE gig (
  gigId INT PRIMARY KEY AUTO_INCREMENT,
  ownerId INT NOT NULL,
  imageUrl VARCHAR(255) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(100) NOT NULL,
  location VARCHAR(255) NOT NULL,
  startDate DATE NOT NULL,
  rateType ENUM('hourly', 'fixed') NOT NULL,
  payRate DECIMAL(10,2) NOT NULL,
  status ENUM('active', 'closed') NOT NULL,
  createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ownerId) REFERENCES users(userId)
);
```

## Operational Features

### 1. Image Upload & Storage
- **Format Support**: JPEG, PNG, GIF, WEBP
- **Size Limit**: 5 MB (enforced at application and nginx levels)
- **Storage**: `app/public/assets/images/`
- **Naming**: `gig-upload-{random_hex}.{extension}`
- **Git Ignore**: Future uploads ignored via `app/public/assets/images/.gitignore`

### 2. Image Lifecycle
| Operation | Action |
|-----------|--------|
| Create Gig | Upload saved if valid, URL stored in DB, file persists |
| Update Gig | New image replaces old, old file deleted after DB update succeeds |
| Delete Gig | Gig deleted from DB, associated image file deleted from disk |
| Failed Create/Update | Uploaded image deleted from disk if DB save fails |

### 3. Ownership & Security
- All gig operations require `PRODUCTION_HOUSE` role
- Ownership verified before accessing/modifying gigs
- URL parameter extraction and validation prevents ID tampering
- Unauthenticated users redirected to `/signin`
- Unauthorized users redirected to `/dashboard`

### 4. Validation
- **Gig Fields**: Required, type-checked, format-validated
- **Images**: Format and size validated before save
- **UI Feedback**: Validation errors displayed inline on forms
- **Atomic Transactions**: Database and filesystem stay consistent

## Request Flow Examples

### Create Gig
```
POST /dashboard/gigs/create
│
├─ DashboardController::handleProductionHouseGigPosting()
│  ├─ Verify role
│  ├─ Get owner ID from session
│  │
│  └─ GigService::create()
│     ├─ Normalize input
│     ├─ Validate input & image
│     ├─ Save image to disk
│     │
│     └─ GigRepository::create()
│        └─ Insert into database
│
└─ Redirect to /dashboard/gigs with success message
```

### Edit Gig
```
POST /dashboard/gigs/{id}/edit
│
├─ DashboardController::handleProductionHouseGigEditing()
│  ├─ Verify role
│  ├─ Extract & validate gig ID
│  ├─ Get owner ID from session
│  │
│  ├─ GigService::findByIdAndVerifyOwnership()
│  │  └─ Verify gig exists & belongs to owner
│  │
│  └─ GigService::update()
│     ├─ Normalize input
│     ├─ Validate input & image (if provided)
│     ├─ Save new image if provided
│     │
│     ├─ GigRepository::update()
│     │  └─ Update database
│     │
│     └─ Delete old image if image was replaced
│
└─ Redirect to /dashboard/gigs with success message
```

### Delete Gig
```
POST /dashboard/gigs/{id}/delete
│
├─ DashboardController::handleProductionHouseGigDeletion()
│  ├─ Verify role
│  ├─ Extract & validate gig ID
│  ├─ Get owner ID from session
│  │
│  └─ GigService::delete()
│     ├─ Find gig by ID
│     ├─ Verify ownership
│     ├─ GigRepository::delete()
│     │  └─ Delete from database
│     │
│     └─ Delete image file from disk
│
└─ Redirect to /dashboard/gigs with success message
```

## URLs & Routes

| HTTP Method | URL | Purpose | Access |
|-------------|-----|---------|--------|
| GET | `/dashboard/gigs` | List production house's gigs | Production House |
| GET | `/dashboard/gigs/create` | Show create form | Production House |
| POST | `/dashboard/gigs/create` | Submit create form | Production House |
| GET | `/dashboard/gigs/{id}/edit` | Show edit form | Production House (owner) |
| POST | `/dashboard/gigs/{id}/edit` | Submit edit form | Production House (owner) |
| POST | `/dashboard/gigs/{id}/delete` | Delete gig | Production House (owner) |

## Error Handling Strategy

### Authentication Errors
- No session: Redirect to `/signin`

### Authorization Errors
- Wrong role: Redirect to `/dashboard`
- Not owner: Redirect to `/dashboard/gigs`

### Validation Errors
- Form re-rendered with errors and old input
- Specific field-level error messages

### Database Errors
- Generic error message displayed
- No sensitive information leaked
- Form re-rendered for retry

### File System Errors
- Image uploads skipped gracefully
- Database changes rolled back if image save fails
- Orphaned files cleaned up automatically

## Related Documentation
- [Gig Listing](GigListing.md) - Display production house gigs
- [Gig Creation](GigCreation.md) - Create new gigs
- [Gig Editing](GigEditing.md) - Update existing gigs
- [Gig Deletion](GigDeletion.md) - Delete gigs
- [Gig Detail](Gig.md) - Public gig detail pages

# FilmGig - Film Industry Gig Marketplace

FilmGig is a web-based platform connecting production houses with freelance creatives in the film and media industry. Production houses can post gig opportunities and review freelancer applications, while freelancers can browse available gigs and apply to those matching their skills.

## Features

### Core Functionality
- **Gig Management**: Production houses can create, edit, and delete gig postings with image uploads
- **Gig Browsing**: Public marketplace for browsing available gigs with filtering and search
- **Freelancer Applications**: Freelancers can apply to gigs and track their submission status
- **Submission Review**: Production houses review and accept/reject freelancer applications for their gigs
- **User Profiles**: Authenticated users can manage their profiles; public profiles showcase portfolios
- **Authentication**: Secure signin/signup with role-based access control (Admin, Production House, Freelancer)

### Architecture
Built with:
- **Backend**: PHP 8.x with PDO MySQL support
- **Web Server**: NGINX
- **Database**: MariaDB
- **Routing**: nikic/FastRoute for modern PHP routing
- **Container**: Docker & Docker Compose for consistent development environment

## Project Structure

```
FilmGig2526/
├── app/                           # Application source code
│   ├── public/                    # Web root (NGINX document root)
│   │   ├── index.php             # Application entry point
│   │   └── assets/               # CSS, JS, images
│   ├── src/
│   │   ├── Controllers/          # Route handlers
│   │   ├── Services/             # Business logic layer
│   │   ├── Repositories/         # Data access layer
│   │   ├── Models/               # Data models
│   │   ├── ViewModels/           # Presentation layer
│   │   ├── Views/                # PHP templates
│   │   ├── Enums/                # Enumerated types
│   │   ├── Framework/            # Core framework classes
│   │   └── Config.php            # Configuration
│   └── composer.json             # PHP dependencies
├── documentation/                # Detailed feature documentation
│   ├── Auth/                     # Authentication flows
│   ├── Dashboard/                # Dashboard and role-based views
│   ├── Gig/                      # Gig management features
│   ├── Submission/               # Submission and application flows
│   ├── Profile/                  # User profile features
│   ├── Home/                     # Homepage
│   └── Settings/                 # User settings
├── docker-compose.yml            # Docker service configuration
├── PHP.Dockerfile               # PHP container configuration
├── nginx.conf                   # NGINX configuration
└── developmentdb.sql           # Database initialization script
```

## Getting Started

### Prerequisites
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose

### Setup Steps

### Running the Application

1. Clone the repository:
```bash
git clone https://github.com/ayazspv/FilmGig2526
cd FilmGig2526
```

2. Start Docker containers:
```bash
docker compose up --build
```

3. Install PHP dependencies (first time only):
```bash
docker compose run --rm php composer install
```

4. Access the application:
- **FilmGig Application**: [http://localhost](http://localhost)
- **PHPMyAdmin**: [http://localhost:8080](http://localhost:8080) (credentials: developer/secret123)

### Stopping the Application

Press `Ctrl+C` in the terminal, or run:
```bash
docker compose down
```

To remove volumes (reset database):
```bash
docker compose down -v
```

## Test Accounts

The development database includes these test credentials:

### Production House
- **Username**: `production`
- **Password**: `secret123`
- **Role**: Production House
- **Capabilities**: Create/edit/delete gigs, review freelancer applications

### Freelancer
- **Username**: `freelancer`
- **Password**: `secret123`
- **Role**: Freelancer
- **Capabilities**: Browse gigs, apply to gigs, manage submissions

## Usage Guide

### For Production Houses

1. Sign in with production house credentials
2. Navigate to **Dashboard** → **My Gigs**
3. Create new gigs with title, description, category, location, rate, and image
4. View applications at **Dashboard** → **Submissions Received**
5. Accept or reject freelancer applications
6. Update company profile and settings

### For Freelancers

1. Sign in with freelancer credentials (or create new account)
2. Navigate to **Browse Gigs** or homepage
3. Browse available gigs with filtering options
4. Click on a gig to view details and apply
5. View your applications at **Dashboard** → **My Applications**
6. Track application status (pending, accepted, rejected)
7. Withdraw from pending applications if needed
8. Update freelancer profile and portfolio

## Development

### Composer Management

This project uses Composer for PSR-4 autoloading with namespace `App\\` mapped to `app/src/`.

**Install dependencies:**
```bash
docker compose run --rm php composer install
```

**Regenerate autoloader** (after adding new classes):
```bash
docker compose run --rm php composer dump-autoload
```

### Database

- **Type**: MariaDB
- **Initialization**: `developmentdb.sql` runs automatically on first container start
- **Admin Tool**: PHPMyAdmin available at http://localhost:8080

The database schema includes tables for:
- Users (with role-based access)
- Production Houses and Freelancers
- Gigs and Submissions
- Session management

### Code Organization

The application follows a layered architecture:

1. **Controllers** (`src/Controllers/`): Handle HTTP requests and route to appropriate actions
2. **Services** (`src/Services/`): Implement business logic and validation
3. **Repositories** (`src/Repositories/`): Manage data access and database operations
4. **Models** (`src/Models/`): Represent domain objects
5. **ViewModels** (`src/ViewModels/`): Format data for presentation
6. **Views** (`src/Views/`): PHP templates for rendering HTML

## Documentation

For detailed feature documentation, see the [documentation](documentation/) folder:

- **[Authentication](documentation/Auth/)**: Signin, signup, password reset
- **[Dashboard](documentation/Dashboard/Dashboard.md)**: Role-based dashboard overview
- **[Gig Management](documentation/Gig/GigManagement.md)**: Creating, editing, deleting gigs
- **[Submissions](documentation/Submission/Submission.md)**: Application workflow
- **[Production House Review](documentation/Submission/ProductionHouseReview.md)**: Reviewing freelancer applications
- **[Freelancer Submissions](documentation/Submission/SubmissionsPage.md)**: Tracking applications
- **[Profiles](documentation/Profile/)**: User profiles and public portfolios
- **[Home](documentation/Home/Home.md)**: Homepage and public browsing

## Compliance & Security

This project includes implementation choices intended to support WCAG accessibility and GDPR privacy/security obligations.

**Note**: This section documents technical controls present in the codebase and is not legal advice.

### WCAG Accessibility Implementation

- **Semantic Structure**: Consistent use of semantic HTML (header/main/section/footer) across views:
  - `app/src/Views/gigs/gigDetail.php`
  - `app/src/Views/dashboards/admin/submissions.php`
  - `app/src/Views/profiles/freelancerPublicProfile.php`

- **Accessible Forms**: Proper `<label>` associations with input `id` attributes:
  - `app/src/Views/auth/signup.php`
  - `app/src/Views/auth/signin.php`
  - `app/src/Views/dashboards/admin/gigPosting.php`

- **Alternative Text**: Descriptive alt attributes for all images and avatars:
  - `app/src/Views/partials/navbars/adminNavbar.php`
  - `app/src/Views/gigs/gigDetail.php`

- **ARIA Support**: ARIA attributes for navigation and dropdown relationships:
  - `app/src/Views/partials/navbars/adminNavbar.php`
  - `app/src/Views/partials/navbars/freelanceNavbar.php`

- **Keyboard Navigation**: Full keyboard support including Enter-to-search and focus management:
  - `app/public/assets/js/main.js`
  - `app/public/assets/js/dashboard.js`

### GDPR Privacy & Security Implementation

- **Password Security**: One-way hash storage, never plaintext:
  - `app/src/Services/SignupService.php`
  - `app/src/Services/PasswordResetService.php`

- **Secure Authentication**: Password verification using secure APIs:
  - `app/src/Services/SigninService.php`

- **Session Management**: Proper cleanup and invalidation on sign-out:
  - `app/src/Framework/Controller.php`
  - `app/src/Controllers/AuthController.php`

- **Input Validation & Output Encoding**: Reduces injection and XSS vulnerabilities:
  - All service layer validation
  - HTML entity encoding in templates

- **Data Rights**: Support for profile updates and password reset (data rectification and account security):
  - `app/src/Controllers/ProfileController.php`
  - `documentation/Auth/ResetPassword.md`

## Troubleshooting

**Port Already in Use**: If port 80 or 8080 is already in use, update `docker-compose.yml` to use different ports.

**Database Connection Error**: Ensure MariaDB container is running. Check with `docker compose ps`.

**Permission Denied NGINX**: Run `docker compose down` and retry. Clear any previous container state with `docker compose down -v`.

## Support & Documentation

For implementation details and feature documentation, see:
- `documentation/README.md` - Full documentation index


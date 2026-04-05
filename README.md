# Docker template for PHP projects
This repository provides a starting template for PHP application development.

It contains:
* NGINX webserver
* PHP FastCGI Process Manager with PDO MySQL support
* MariaDB (GPL MySQL fork)
* PHPMyAdmin
* Composer
* Composer package [nikic/fast-route](https://github.com/nikic/FastRoute) for routing

## Setup

1. Install Docker Desktop on Windows or Mac, or Docker Engine on Linux.
1. Clone the project

## Login

Login as a production house
username: production
password: secret123

Login as a freelancer
username: freelancer
password: secret123

## Usage

In a terminal, from the cloned project folder, run:
```bash
docker compose up
```

### Composer Autoload

This template is configured to use Composer for PSR-4 autoloading:

- Namespace `App\\` is mapped to `app/src/`.

To install dependencies and generate the autoloader, run:

```bash
docker compose run --rm php composer install
```

If you add new classes or change namespaces, regenerate the autoloader:

```bash
docker compose run --rm php composer dump-autoload
```

Example usage is wired in `app/public/index.php` and a sample class exists at `app/src/hello.php`.

### NGINX

NGINX will now serve files in the app/public folder.

Go to [http://localhost/hello.php](http://localhost/hello.php). You should see a hello world message.

### PHPMyAdmin

PHPMyAdmin provides basic database administration. It is accessible at [localhost:8080](localhost:8080).

Credentials are defined in `docker-compose.yml`. They are: developer/secret123

## Compliance Notes (WCAG & GDPR)

This project includes implementation choices intended to support WCAG accessibility and GDPR privacy/security obligations.

Note: this section documents technical controls present in the codebase and is not legal advice.

### WCAG-related implementation references

- Semantic structure and page regions (header/main/section) used consistently in views:
	- `app/src/Views/gigs/gigDetail.php`
	- `app/src/Views/dashboards/admin/submissions.php`
	- `app/src/Views/profiles/freelancerPublicProfile.php`
- Accessible labels and form structure (`label for=...` + input ids):
	- `app/src/Views/auth/signup.php`
	- `app/src/Views/auth/signin.php`
	- `app/src/Views/dashboards/admin/gigPosting.php`
- Alternative text for images and descriptive avatar/hero alt attributes:
	- `app/src/Views/partials/navbars/adminNavbar.php`
	- `app/src/Views/partials/navbars/freelanceNavbar.php`
	- `app/src/Views/gigs/gigDetail.php`
- ARIA usage for navigation controls and dropdown relationships:
	- `app/src/Views/partials/navbars/adminNavbar.php`
	- `app/src/Views/partials/navbars/freelanceNavbar.php`
- Keyboard interaction support (e.g., Enter-to-search and focus-aware behavior):
	- `app/public/assets/js/home.js`
	- `app/public/assets/js/main.js`

### GDPR-related implementation references

- Passwords are stored as one-way hashes (never plaintext):
	- `app/src/Services/SignupService.php`
	- `app/src/Services/PasswordResetService.php`
- Sign-in verifies hashed passwords using secure password APIs:
	- `app/src/Services/SigninService.php`
- Session cleanup on sign-out includes session data wipe + cookie invalidation:
	- `app/src/Framework/Controller.php`
	- `app/src/Controllers/AuthController.php`
- Input validation and output encoding to reduce injection/XSS risk:
	- `app/src/Services/SignupService.php`
	- `app/src/Services/ProfileService.php`
	- `app/src/Views/partials/header.php`
	- `app/src/Views/gigs/gigDetail.php`
- User profile update and password reset flows (data rectification/account security support):
	- `app/src/Controllers/ProfileController.php`
	- `app/src/Controllers/AuthController.php`
	- `documentation/Profile/ProfileEditing.md`
	- `documentation/Auth/ResetPassword.md`


### Stopping the docker container

If you want to stop the containers, press Ctrl+C. 

Or run:
```bash
docker compose down
```


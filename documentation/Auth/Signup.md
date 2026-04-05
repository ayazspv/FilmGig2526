# Signup

## Purpose
Create a new account and the matching role-specific record for either a production company or a freelancer.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showSignupForm()`
- `AuthController::handleSignupForm()`
- `AuthController::renderSignup()`
- `Controller::redirect()`

## Model Files Used
- [app/src/Models/User.php](../../app/src/Models/User.php)
- [app/src/Models/ProductionHouse.php](../../app/src/Models/ProductionHouse.php)
- [app/src/Models/Freelancer.php](../../app/src/Models/Freelancer.php)

### Methods Used
- `User::getUserId()`
- `User::getRole()`
- `ProductionHouse` and `Freelancer` are populated through their repositories after the base user is created

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)
- [app/src/Repositories/Interfaces/IUserRepository.php](../../app/src/Repositories/Interfaces/IUserRepository.php)
- [app/src/Repositories/Interfaces/IProductionHouseRepository.php](../../app/src/Repositories/Interfaces/IProductionHouseRepository.php)
- [app/src/Repositories/Interfaces/IFreelancerRepository.php](../../app/src/Repositories/Interfaces/IFreelancerRepository.php)

### Methods Used
- `UserRepository::create()`
- `UserRepository::usernameExists()`
- `UserRepository::emailExists()`
- `UserRepository::kvkNrExists()`
- `ProductionHouseRepository::create()`
- `FreelancerRepository::create()`

## Service Files Used
- [app/src/Services/SignupService.php](../../app/src/Services/SignupService.php)
- [app/src/Services/Interfaces/ISignupService.php](../../app/src/Services/Interfaces/ISignupService.php)
- [app/src/Framework/Service.php](../../app/src/Framework/Service.php)

### Methods Used
- `SignupService::register()`
- `SignupService::normalizeInput()`
- `SignupService::validate()`
- `SignupService::validateUsername()`
- `SignupService::validateName()`
- `SignupService::validateEmail()`
- `SignupService::validatePassword()`
- `SignupService::validateRole()`
- `SignupService::validateKvkNr()`
- `SignupService::validateRoleSpecificFields()`
- `SignupService::validateProductionHouseFields()`
- `SignupService::validateFreelancerFields()`
- `SignupService::registerInTransaction()`
- `SignupService::createUser()`
- `SignupService::createRoleRecord()`
- `SignupService::buildFailureResult()`
- `SignupService::buildSuccessResult()`
- `SignupService::isValidDate()`
- `SignupService::isAtLeast18YearsOld()` - Validates freelancer is at least 18 years old

## Input Validation

All fields are validated on the server and render errors inline on the form if validation fails.

### Common Fields (All Roles)
| Field | Requirements | Error Message |
|-------|--------------|---------------|
| `username` | Not empty, alphanumeric + dots only, unique | "Username is required." / "Username may only contain letters, numbers, and dots (.)." / "This username is already taken." |
| `name` | Not empty | "Full name is required." |
| `email` | Valid email format, unique | "Email address is required." / "Please enter a valid email address." / "An account with this email already exists." |
| `password` | At least 8 characters | "Password is required." / "Password must be at least 8 characters long." |
| `role` | Valid role (ProductionHouse or Freelancer, not Admin) | "Please choose a valid account type." |
| `kvkNr` | 8 digits, unique | "Chamber of Commerce number is required." / "Chamber of Commerce number must contain exactly 8 digits." / "This Chamber of Commerce number is already registered." |
| `address` | Optional | - |
| `bio` | Optional | - |

### Production House Fields
| Field | Requirements | Error Message |
|-------|--------------|---------------|
| `companyName` | Not empty | "Company name is required for production companies." |
| `website` | Optional, valid URL if provided | "Website must be a valid URL." |

### Freelancer Fields
| Field | Requirements | Error Message |
|-------|--------------|---------------|
| `dateOfBirth` | Valid YYYY-MM-DD date format, must make person at least 18 years old | "Date of birth is required for freelancers." / "Please enter a valid date of birth." / "You must be at least 18 years old to sign up." |

## Flow
1. The user opens `/signup`.
2. `AuthController::showSignupForm()` renders the signup form with an `<input type="date" max="...">` that prevents selection of dates making the person under 18.
3. The user selects a role and submits the account details.
4. `AuthController::handleSignupForm()` forwards the request to `SignupService::register()`.
5. `SignupService` normalizes the input and runs focused validation helpers for each field.
6. The service checks username, email, and KVK uniqueness through `UserRepository`.
7. For freelancers, the service validates date of birth format and calls `isAtLeast18YearsOld()` to verify the person is at least 18 years old.
8. If the input is valid, the service opens a transaction and creates the base user record.
9. The service creates the role-specific record using `ProductionHouseRepository` or `FreelancerRepository`.
10. On success, the controller stores a flash message and redirects to `/signin`.
11. On failure, the signup form is rendered again with validation errors and old input.

# Signup

## Purpose
Create a new user account and the matching role-specific record for either a production company or a freelancer.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showSignupForm()`
- `AuthController::handleSignupForm()`
- `Controller::redirect()`

## Model Files Used
- [app/src/Models/User.php](../../app/src/Models/User.php)
- [app/src/Models/ProductionHouse.php](../../app/src/Models/ProductionHouse.php)
- [app/src/Models/Freelancer.php](../../app/src/Models/Freelancer.php)

### Methods Used
- `User::getUserId()` is used after signup to continue role-based setup in related flows
- `User::getRole()` is used in login/dashboard flows after the account is created
- `ProductionHouse` and `Freelancer` are mapped by their repositories when the account is expanded into a role record

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)
- [app/src/Repositories/Interfaces/IUserRepository.php](../../app/src/Repositories/Interfaces/IUserRepository.php)

### Methods Used
- `UserRepository::create()`
- `UserRepository::usernameExists()`
- `UserRepository::emailExists()`
- `UserRepository::kvkNrExists()`
- `ProductionHouseRepository::create()`
- `FreelancerRepository::create()`

## Service Files Used
- [app/src/Services/SignupService.php](../../app/src/Services/SignupService.php)

### Methods Used
- `SignupService::register()`
- `SignupService::validate()`
- `SignupService::normalizeInput()`
- `SignupService::isValidDate()`

## Flow
1. The user opens `/signup`.
2. `AuthController::showSignupForm()` renders the signup form.
3. The user selects a role and enters account details.
4. `AuthController::handleSignupForm()` passes the POST payload to `SignupService::register()`.
5. `SignupService` normalizes the input and validates username, email, password, role, KVK number, and the role-specific fields.
6. The service checks username uniqueness with `UserRepository::usernameExists()`, email uniqueness with `UserRepository::emailExists()`, and KVK uniqueness with `UserRepository::kvkNrExists()`.
7. If the data is valid, the service creates the base user record with `UserRepository::create()`.
8. The service then creates the matching role record with either `ProductionHouseRepository::create()` or `FreelancerRepository::create()` inside the same transaction.
9. On success, the controller stores a flash message in session and redirects to `/signin`.
10. On failure, the signup form is shown again with validation errors and previous input.

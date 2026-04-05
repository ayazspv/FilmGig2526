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

## Flow
1. The user opens `/signup`.
2. `AuthController::showSignupForm()` renders the signup form.
3. The user selects a role and submits the account details.
4. `AuthController::handleSignupForm()` forwards the request to `SignupService::register()`.
5. `SignupService` normalizes the input and runs focused validation helpers for each field.
6. The service checks username, email, and KVK uniqueness through `UserRepository`.
7. If the input is valid, the service opens a transaction and creates the base user record.
8. The service creates the role-specific record using `ProductionHouseRepository` or `FreelancerRepository`.
9. On success, the controller stores a flash message and redirects to `/signin`.
10. On failure, the signup form is rendered again with validation errors and old input.

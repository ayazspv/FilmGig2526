# Signin

## Purpose
Allow an existing user to log in with their username and password, then route them to the correct dashboard based on role.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showSigninForm()`
- `AuthController::handleSigninForm()`
- `Controller::isAuthenticated()`
- `Controller::redirect()`

## Model Files Used
- [app/src/Models/User.php](../../app/src/Models/User.php)

### Methods Used
- `User::getUserId()`
- `User::getUsername()`
- `User::getName()`
- `User::getPassword()`
- `User::getRole()`

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/Interfaces/IUserRepository.php](../../app/src/Repositories/Interfaces/IUserRepository.php)

### Methods Used
- `UserRepository::findByUsername()`
- `UserRepository::emailExists()` is not used in signin, but remains available for signup flows
- `IUserRepository::findByUsername()`

## Service Files Used
- [app/src/Services/SigninService.php](../../app/src/Services/SigninService.php)

### Methods Used
- `SigninService::authenticate()`

## Flow
1. The user opens `/signin`.
2. `AuthController::showSigninForm()` renders the signin view and optionally shows signup/reset success messages.
3. The user submits username and password.
4. `AuthController::handleSigninForm()` delegates validation and authentication to `SigninService::authenticate()`.
5. `SigninService` normalizes the input, checks required fields, and loads the user by username through `UserRepository::findByUsername()`.
6. The service verifies the password with `password_verify()` and checks that the role is valid.
7. On success, `AuthController` stores the user session data and redirects to `/dashboard`.
8. On failure, the signin form is rendered again with validation or authentication errors.

# Signin

## Purpose
Allow an existing user to log in with a username and password, then route them to the correct dashboard based on role.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showSigninForm()`
- `AuthController::handleSigninForm()`
- `AuthController::renderSignin()`
- `Controller::isAuthenticated()`
- `Controller::redirect()`

## Model Files Used
- [app/src/Models/User.php](../../app/src/Models/User.php)

### Methods Used
- `User::getUserId()`
- `User::getName()`
- `User::getPassword()`
- `User::getRole()`

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/Interfaces/IUserRepository.php](../../app/src/Repositories/Interfaces/IUserRepository.php)

### Methods Used
- `UserRepository::findByUsername()`
- `IUserRepository::findByUsername()`

## Service Files Used
- [app/src/Services/SigninService.php](../../app/src/Services/SigninService.php)
- [app/src/Services/Interfaces/ISigninService.php](../../app/src/Services/Interfaces/ISigninService.php)
- [app/src/Framework/Service.php](../../app/src/Framework/Service.php)

### Methods Used
- `SigninService::authenticate()`
- `SigninService::normalizeInput()`
- `SigninService::validate()`
- `SigninService::findUserByUsername()`
- `SigninService::isValidPassword()`
- `SigninService::hasSupportedRole()`
- `SigninService::buildFailureResult()`
- `SigninService::buildRepositoryFailureResult()`
- `SigninService::buildAuthenticationFailureResult()`
- `SigninService::buildUnsupportedRoleFailureResult()`
- `SigninService::buildSuccessResult()`

## Flow
1. The user opens `/signin`.
2. `AuthController::showSigninForm()` renders the signin page and shows any flash success message from signup or password reset.
3. The user submits username and password.
4. `AuthController::handleSigninForm()` passes the request payload to `SigninService::authenticate()`.
5. `SigninService` normalizes and validates the input.
6. The service loads the user with `UserRepository::findByUsername()`.
7. The service verifies the password and checks that the role is supported.
8. On success, the controller stores the authenticated user data in session and redirects to `/dashboard`.
9. On failure, the signin view is rendered again with validation or authentication errors.

# Forget Password

## Purpose
Verify a user by username, email, and KVK number before allowing password reset.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showForgetPasswordForm()`
- `AuthController::handleForgetPasswordForm()`
- `AuthController::renderForgetPassword()`
- `Controller::redirect()`

## Model Files Used
- [app/src/Models/User.php](../../app/src/Models/User.php)

### Methods Used
- `User::getUserId()`
- `User::getUsername()`
- `User::getEmail()`
- `User::getKvkNr()`

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/Interfaces/IUserRepository.php](../../app/src/Repositories/Interfaces/IUserRepository.php)

### Methods Used
- `UserRepository::findByUsername()`
- `IUserRepository::findByUsername()`

## Service Files Used
- [app/src/Services/PasswordResetService.php](../../app/src/Services/PasswordResetService.php)
- [app/src/Services/Interfaces/IPasswordResetService.php](../../app/src/Services/Interfaces/IPasswordResetService.php)
- [app/src/Framework/Service.php](../../app/src/Framework/Service.php)

### Methods Used
- `PasswordResetService::verifyIdentity()`
- `PasswordResetService::normalizeIdentityInput()`
- `PasswordResetService::validateIdentityInput()`
- `PasswordResetService::findUserByUsername()`
- `PasswordResetService::buildFailureResult()`
- `PasswordResetService::buildVerifySuccessResult()`

## Flow
1. The user opens `/forget-password`.
2. `AuthController::showForgetPasswordForm()` renders the identity verification form.
3. The user enters username, email, and KVK number.
4. `AuthController::handleForgetPasswordForm()` sends the request to `PasswordResetService::verifyIdentity()`.
5. The service normalizes and validates the submitted values.
6. The service loads the account by username through `UserRepository::findByUsername()`.
7. The service compares the stored email and KVK number against the submitted values.
8. If the values match, the controller stores the verified user id and username in session and redirects to `/reset-password`.
9. If the values do not match, the form is rendered again with validation or verification errors.

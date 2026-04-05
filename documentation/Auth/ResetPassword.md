# Reset Password

## Purpose
Allow a previously verified user to set a new password without using a code.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showResetPasswordForm()`
- `AuthController::handleResetPasswordForm()`
- `AuthController::renderResetPassword()`
- `Controller::redirect()`

## Model Files Used
- [app/src/Models/User.php](../../app/src/Models/User.php)

### Methods Used
- `User::getUserId()`
- `User::getUsername()`

## Repository Files Used
- [app/src/Repositories/UserRepository.php](../../app/src/Repositories/UserRepository.php)
- [app/src/Repositories/Interfaces/IUserRepository.php](../../app/src/Repositories/Interfaces/IUserRepository.php)

### Methods Used
- `UserRepository::updatePassword()`
- `IUserRepository::updatePassword()`

## Service Files Used
- [app/src/Services/PasswordResetService.php](../../app/src/Services/PasswordResetService.php)
- [app/src/Services/Interfaces/IPasswordResetService.php](../../app/src/Services/Interfaces/IPasswordResetService.php)
- [app/src/Framework/Service.php](../../app/src/Framework/Service.php)

### Methods Used
- `PasswordResetService::resetPassword()`
- `PasswordResetService::normalizePasswordInput()`
- `PasswordResetService::validatePasswordInput()`
- `PasswordResetService::updatePassword()`
- `PasswordResetService::buildFailureResult()`

## Flow
1. The user reaches `/reset-password` after identity verification.
2. `AuthController::showResetPasswordForm()` confirms that a reset session exists.
3. The reset form is shown with the verified username.
4. The user enters a new password and confirmation.
5. `AuthController::handleResetPasswordForm()` sends the request to `PasswordResetService::resetPassword()`.
6. The service validates password length and confirmation match.
7. If valid, the service hashes the password and calls `UserRepository::updatePassword()`.
8. The reset session is cleared and the user is redirected to `/signin` with a success message.

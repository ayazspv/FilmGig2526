# Forget Password

## Purpose
Verify a user by username, email, and KVK number before allowing password reset.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::showForgetPasswordForm()`
- `AuthController::handleForgetPasswordForm()`
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

### Methods Used
- `PasswordResetService::verifyIdentity()`
- `PasswordResetService::validateIdentityInput()`
- `PasswordResetService::normalizeIdentityInput()`

## Flow
1. The user opens `/forget-password`.
2. `AuthController::showForgetPasswordForm()` renders the verification form.
3. The user enters username, email, and KVK number.
4. `AuthController::handleForgetPasswordForm()` sends the data to `PasswordResetService::verifyIdentity()`.
5. The service validates the fields and loads the user by username through `UserRepository::findByUsername()`.
6. The service compares the stored email and KVK number with the submitted values.
7. If the values match, the controller stores the verified user id and username in the session and redirects to `/reset-password`.
8. If the data does not match, the form is rendered again with an error message.

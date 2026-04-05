# Signout

## Purpose
End the current session and send the user back to the signin page.

## Controller Files Used
- [app/src/Controllers/AuthController.php](../../app/src/Controllers/AuthController.php)
- [app/src/Framework/Controller.php](../../app/src/Framework/Controller.php)

### Methods Used
- `AuthController::handleSignout()`
- `Controller::destroySession()`
- `Controller::redirect()`

## Model Files Used
- None directly.

## Repository Files Used
- None directly.

## Service Files Used
- None directly.

## Flow
1. The user clicks Log Out from the navbar.
2. The route `/signout` calls `AuthController::handleSignout()`.
3. The controller clears the session with `Controller::destroySession()`.
4. The controller redirects to `/signin`.

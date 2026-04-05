# Home

## Purpose
Render the public homepage shell and populate it with live content from a JSON API.

## Controller Files Used
- [app/src/Controllers/HomeController.php](../../app/src/Controllers/HomeController.php)

### Methods Used
- `HomeController::index()`
- `HomeController::apiIndex()`
- `HomeController::findLatestActiveGigs()`
- `HomeController::countActiveGigs()`

## Model Files Used
- [app/src/ViewModels/HomeViewModel.php](../../app/src/ViewModels/HomeViewModel.php)

### Methods Used
- `HomeViewModel::createFromData()`
- `HomeViewModel::createDefault()`
- `HomeViewModel::createFromGigs()`
- `HomeViewModel::toArray()`

## Repository Files Used
- [app/src/Repositories/GigRepository.php](../../app/src/Repositories/GigRepository.php)
- [app/src/Repositories/ProductionHouseRepository.php](../../app/src/Repositories/ProductionHouseRepository.php)
- [app/src/Repositories/FreelancerRepository.php](../../app/src/Repositories/FreelancerRepository.php)
- [app/src/Repositories/SubmissionRepository.php](../../app/src/Repositories/SubmissionRepository.php)

## Frontend Files Used
- [app/public/assets/js/main.js](../../app/public/assets/js/main.js)
- [app/src/Views/home.php](../../app/src/Views/home.php)
- [app/src/Views/home/hero.php](../../app/src/Views/home/hero.php)
- [app/src/Views/home/gigs.php](../../app/src/Views/home/gigs.php)
- [app/src/Views/home/howItWorks.php](../../app/src/Views/home/howItWorks.php)
- [app/src/Views/home/whyFilmGig.php](../../app/src/Views/home/whyFilmGig.php)

## Flow
1. The user opens `/`.
2. `HomeController::index()` renders a lightweight shell with the `/api/home` endpoint attached to the page.
3. `main.js` fetches the JSON payload from `/api/home`.
4. `HomeController::apiIndex()` builds the live homepage payload from the repositories.
5. `HomeViewModel::toArray()` serializes hero, how-it-works, gigs, and why-FilmGig data into JSON.
6. The frontend renders the live payload into the home sections.

## Data Sources
- Hero stats come from the current number of active gigs, production houses, freelancers, and submissions.
- Featured gig cards come from the latest active gigs in the database.
- The “Why FilmGig?” bullets are generated from the current live platform counts.

## Notes
- The homepage no longer relies on server-side section data for the visible content.
- The shell stays stable while the API data is loading.

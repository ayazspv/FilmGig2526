<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/init.php';

use App\Framework\Router;

$router = new Router();

// Define your routes here
// Template: $router->addRoute('METHOD', '/path/{param}', ['ControllerClass', 'methodName']);

// Non-auth routes
$router->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);
$router->addRoute('GET', '/api/home', ['App\Controllers\HomeController', 'apiIndex']);

// Auth routes
$router->addRoute('GET', '/signup', ['App\Controllers\AuthController', 'showSignupForm']);
$router->addRoute('POST', '/signup', ['App\Controllers\AuthController', 'handleSignupForm']);

$router->addRoute('GET', '/signin', ['App\Controllers\AuthController', 'showSigninForm']);
$router->addRoute('POST', '/signin', ['App\Controllers\AuthController', 'handleSigninForm']);
$router->addRoute('GET', '/signout', ['App\Controllers\AuthController', 'handleSignout']);

$router->addRoute('GET', '/reset-password', ['App\Controllers\AuthController', 'showResetPasswordForm']);
$router->addRoute('POST', '/reset-password', ['App\Controllers\AuthController', 'handleResetPasswordForm']);
$router->addRoute('GET', '/forget-password', ['App\Controllers\AuthController', 'showForgetPasswordForm']);
$router->addRoute('POST', '/forget-password', ['App\Controllers\AuthController', 'handleForgetPasswordForm']);

// Dashboard routes
$router->addRoute('GET', '/dashboard', ['App\Controllers\DashboardController', 'showDashboard']);
$router->addRoute('GET', '/api/dashboard', ['App\Controllers\DashboardController', 'apiDashboard']);

$router->addRoute('GET', '/dashboard/gigs', ['App\Controllers\DashboardController', 'showProductionHouseGigListing']);
$router->addRoute('GET', '/dashboard/gigs/new', ['App\Controllers\DashboardController', 'showProductionHouseGigPosting']);
$router->addRoute('POST', '/dashboard/gigs/new', ['App\Controllers\DashboardController', 'handleProductionHouseGigPosting']);
$router->addRoute('GET', '/dashboard/gigs/{id}', ['App\Controllers\DashboardController', 'showProductionHouseGigEditing']);
$router->addRoute('POST', '/dashboard/gigs/{id}', ['App\Controllers\DashboardController', 'handleProductionHouseGigEditing']);
$router->addRoute('POST', '/dashboard/gigs/{id}/delete', ['App\Controllers\DashboardController', 'handleProductionHouseGigDeletion']);
$router->addRoute('GET', '/dashboard/submissions', ['App\Controllers\DashboardController', 'showFreelancerSubmissions']);
$router->addRoute('POST', '/dashboard/submissions/{id}/withdraw', ['App\Controllers\DashboardController', 'handleFreelancerSubmissionWithdrawal']);
$router->addRoute('GET', '/dashboard/submissions/received', ['App\Controllers\DashboardController', 'showProductionHouseSubmissions']);
$router->addRoute('POST', '/dashboard/submissions/{id}/review', ['App\Controllers\DashboardController', 'handleProductionHouseSubmissionReview']);





$router->addRoute('GET', '/profile', ['App\Controllers\ProfileController', 'showProfile']);
$router->addRoute('GET', '/profiles/production-house/{id}', ['App\Controllers\ProfileController', 'showProductionHousePublicProfile']);
$router->addRoute('GET', '/profiles/freelancer/{id}', ['App\Controllers\ProfileController', 'showFreelancerPublicProfile']);
$router->addRoute('POST', '/profile', ['App\Controllers\ProfileController', 'handleProfileUpdate']);
$router->addRoute('GET', '/settings', ['App\Controllers\SettingsController', 'showSettings']);

$router->addRoute('GET', '/gigs', ['App\Controllers\GigController', 'showGigListing']);
$router->addRoute('GET', '/api/gigs', ['App\Controllers\GigController', 'apiGigs']);
$router->addRoute('GET', '/gigs/{id}', ['App\Controllers\GigController', 'showGigDetail']);
$router->addRoute('POST', '/gigs/{id}/apply', ['App\Controllers\GigController', 'handleGigApplication']);

// Dispatch the request
$router->dispatch();

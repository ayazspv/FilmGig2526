<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/init.php';

use App\Framework\Router;

$router = new Router();

// Define your routes here
// Template: $router->addRoute('METHOD', '/path/{param}', ['ControllerClass', 'methodName']);

// Auth routes
$router->addRoute('GET', '/signup', ['App\Controllers\AuthController', 'showSignupForm']);
$router->addRoute('POST', '/signup', ['App\Controllers\AuthController', 'handleSignupForm']);


$router->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

$router->addRoute('GET', '/signin', ['App\Controllers\AuthController', 'showSigninForm']);
$router->addRoute('POST', '/signin', ['App\Controllers\AuthController', 'showSigninForm']);
$router->addRoute('GET', '/reset-password', ['App\Controllers\AuthController', 'showResetPasswordForm']);
$router->addRoute('GET', '/forget-password', ['App\Controllers\AuthController', 'showForgetPasswordForm']);

$router->addRoute('GET', '/dashboard/admin', ['App\Controllers\DashboardController', 'showAdminDashboard']);
$router->addRoute('GET', '/dashboard/admin/settings', ['App\Controllers\SettingsController', 'showAdminSettings']);
$router->addRoute('GET', '/dashboard/admin/gigs/new', ['App\Controllers\DashboardController', 'showAdminGigPosting']);
$router->addRoute('GET', '/dashboard/admin/gigs/{id}', ['App\Controllers\DashboardController', 'showAdminGigEditing']);
$router->addRoute('GET', '/dashboard/admin/gigs', ['App\Controllers\DashboardController', 'showAdminGigListing']);

$router->addRoute('GET', '/dashboard/freelancer', ['App\Controllers\DashboardController', 'showFreelancerDashboard']);
$router->addRoute('GET', '/dashboard/freelancer/settings', ['App\Controllers\SettingsController', 'showFreelancerSettings']);

$router->addRoute('GET', '/profile/admin', ['App\Controllers\ProfileController', 'showAdminProfile']);
$router->addRoute('GET', '/profile/freelancer', ['App\Controllers\ProfileController', 'showFreelancerProfile']);

$router->addRoute('GET', '/gigs', ['App\Controllers\GigController', 'showGigListing']);
$router->addRoute('GET', '/gigs/{id}', ['App\Controllers\GigController', 'showGigDetail']);

// Dispatch the request
$router->dispatch();

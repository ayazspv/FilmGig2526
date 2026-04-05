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

$router->addRoute('GET', '/signin', ['App\Controllers\AuthController', 'showSigninForm']);
$router->addRoute('POST', '/signin', ['App\Controllers\AuthController', 'handleSigninForm']);
$router->addRoute('GET', '/signout', ['App\Controllers\AuthController', 'handleSignout']);


$router->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

$router->addRoute('GET', '/reset-password', ['App\Controllers\AuthController', 'showResetPasswordForm']);
$router->addRoute('POST', '/reset-password', ['App\Controllers\AuthController', 'handleResetPasswordForm']);
$router->addRoute('GET', '/forget-password', ['App\Controllers\AuthController', 'showForgetPasswordForm']);
$router->addRoute('POST', '/forget-password', ['App\Controllers\AuthController', 'handleForgetPasswordForm']);

$router->addRoute('GET', '/dashboard', ['App\Controllers\DashboardController', 'showDashboard']);

$router->addRoute('GET', '/dashboard/admin/gigs/new', ['App\Controllers\DashboardController', 'showAdminGigPosting']);
$router->addRoute('GET', '/dashboard/admin/gigs/{id}', ['App\Controllers\DashboardController', 'showAdminGigEditing']);
$router->addRoute('GET', '/dashboard/admin/gigs', ['App\Controllers\DashboardController', 'showAdminGigListing']);

$router->addRoute('GET', '/profile', ['App\Controllers\ProfileController', 'showProfile']);
$router->addRoute('GET', '/settings', ['App\Controllers\SettingsController', 'showSettings']);

$router->addRoute('GET', '/gigs', ['App\Controllers\GigController', 'showGigListing']);
$router->addRoute('GET', '/gigs/{id}', ['App\Controllers\GigController', 'showGigDetail']);

// Dispatch the request
$router->dispatch();

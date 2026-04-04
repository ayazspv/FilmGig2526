<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/init.php';

use App\Framework\Router;

$router = new Router();

// Define your routes here
// Template: $router->addRoute('METHOD', '/path/{param}', ['ControllerClass', 'methodName']);

$router->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

$router->addRoute('GET', '/signin', ['App\Controllers\AuthController', 'showSigninForm']);
$router->addRoute('GET', '/reset-password', ['App\Controllers\AuthController', 'showResetPasswordForm']);
$router->addRoute('GET', '/signup', ['App\Controllers\AuthController', 'showSignupForm']);
$router->addRoute('GET', '/forget-password', ['App\Controllers\AuthController', 'showForgetPasswordForm']);

// Dispatch the request
$router->dispatch();

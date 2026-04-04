<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Framework\Router;

$router = new Router();

// Define your routes here
// Template: $router->addRoute('METHOD', '/path/{param}', ['ControllerClass', 'methodName']);
$router->addRoute('GET', '/', ['App\Controllers\WelcomeController', 'show']);

// Dispatch the request
$router->dispatch();

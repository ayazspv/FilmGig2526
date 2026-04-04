<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/init.php';

use App\Framework\Router;

$router = new Router();

// Define your routes here
// Template: $router->addRoute('METHOD', '/path/{param}', ['ControllerClass', 'methodName']);

$router->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

// Dispatch the request
$router->dispatch();

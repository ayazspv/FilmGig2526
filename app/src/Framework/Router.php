<?php

namespace App\Framework;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

/**
 * Router class to handle all routing logic for the application.
 */
class Router
{
    private array $routes = [];
    private $dispatcher;

    /**
     * Add a route to the router.
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path Route path (e.g., '/hello/{name}')
     * @param array $handler [ControllerClass, methodName]
     */
    public function addRoute(string $method, string $path, array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
        ];
    }

    /**
     * Initialize the dispatcher with all registered routes.
     * This is called automatically by dispatch().
     */
    private function initializeDispatcher(): void
    {
        $this->dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $route) {
                $r->addRoute($route['method'], $route['path'], $route['handler']);
            }
        });
    }

    /**
     * Dispatch the current request and invoke the appropriate controller.
     * This method handles all routing logic and controller invocation.
     */
    public function dispatch(): void
    {
        // Initialize dispatcher with all defined routes
        $this->initializeDispatcher();

        // Get request data from server variables
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        // Handle the dispatch result
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $this->handleNotFound();
                break;

            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $this->handleMethodNotAllowed();
                break;

            case \FastRoute\Dispatcher::FOUND:
                $this->invokeController($routeInfo[1], $routeInfo[2]);
                break;
        }
    }

    /**
     * Invoke the controller method with route parameters.
     * 
     * @param array $handler [ControllerClass, methodName]
     * @param array $params Route parameters (e.g., ['name' => 'value'])
     */
    private function invokeController(array $handler, array $params): void
    {
        [$controllerClass, $methodName] = $handler;

        // Create an instance of the controller
        $controller = new $controllerClass();

        // Call the method with parameters
        $controller->$methodName($params);
    }

    /**
     * Handle 404 Not Found errors.
     * Override this method in a subclass or use a custom error handler.
     */
    protected function handleNotFound(): void
    {
        http_response_code(404);

        $pageTitle = '404 - Page Not Found';
        $statusCode = 404;
        $errorTitle = 'Page Not Found';
        $errorMessage = 'Sorry, the page you\'re looking for doesn\'t exist or has been moved.';
        $actions = [
            ['url' => '/', 'label' => 'Go Back Home', 'class' => 'btn btn-primary'],
            ['url' => 'javascript:history.back()', 'label' => 'Go Back', 'class' => 'btn btn-secondary'],
        ];

        include __DIR__ . '/../Views/404.php';
    }

    /**
     * Handle 405 Method Not Allowed errors.
     * Override this method in a subclass or use a custom error handler.
     */
    protected function handleMethodNotAllowed(): void
    {
        http_response_code(405);
        echo 'Method Not Allowed';
    }

    /**
     * HTTP redirect helper for controllers or router-level flow.
     */
    public function redirect(string $path, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header('Location: ' . $path);
        exit;
    }
}

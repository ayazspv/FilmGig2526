<?php

namespace App\Controllers;

class WelcomeController
{
    /**
     * Display the welcome page
     * 
     * @param array $params Route parameters (empty for this route)
     */
    public function show(array $params = []): void
    {
        include __DIR__ . '/../Views/welcome.php';
    }
}

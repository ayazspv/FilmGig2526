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
        $pageTitle = 'Welcome - FilmGig';
        $headline = 'Welcome to FilmGig';
        $subtitle = 'Your Film Event Management Platform';
        $description = 'FilmGig is a modern web application built with a custom PHP framework using FastRoute for efficient routing. This welcome page is here to test that the routing logic is working correctly!';
        $features = [
            'Router is working correctly',
            'WelcomeController is properly instantiated',
            'Route parameters are being handled',
            'View rendering is functioning',
        ];
        $actions = [
            ['url' => '/hello/Traveler', 'label' => 'Test Dynamic Route', 'class' => 'btn btn-primary'],
            ['url' => '/not-found', 'label' => 'Test 404 Page', 'class' => 'btn btn-secondary'],
        ];

        include __DIR__ . '/../Views/welcome.php';
    }
}

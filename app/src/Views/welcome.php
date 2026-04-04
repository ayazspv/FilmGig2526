<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - FilmGig</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <div class="container">
        <main class="welcome-container">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome to FilmGig</h1>
                <p class="welcome-subtitle">Your Film Event Management Platform</p>
                <p class="welcome-description">
                    FilmGig is a modern web application built with a custom PHP framework using FastRoute for efficient routing.
                    This welcome page is here to test that the routing logic is working correctly!
                </p>
                
                <div class="feature-list">
                    <h3>Testing Information:</h3>
                    <ul>
                        <li>✓ Router is working correctly</li>
                        <li>✓ WelcomeController is properly instantiated</li>
                        <li>✓ Route parameters are being handled</li>
                        <li>✓ View rendering is functioning</li>
                    </ul>
                </div>
                
                <div class="welcome-actions">
                    <a href="/hello/Traveler" class="btn btn-primary">Test Dynamic Route</a>
                    <a href="/not-found" class="btn btn-secondary">Test 404 Page</a>
                </div>
            </div>
            
            <div class="welcome-illustration">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <!-- Film reel icon -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="4"/>
                    <circle cx="100" cy="100" r="60" fill="none" stroke="currentColor" stroke-width="4"/>
                    <circle cx="100" cy="100" r="30" fill="none" stroke="currentColor" stroke-width="4"/>
                    
                    <!-- Film holes -->
                    <circle cx="100" cy="40" r="8" fill="currentColor"/>
                    <circle cx="140" cy="70" r="8" fill="currentColor"/>
                    <circle cx="160" cy="100" r="8" fill="currentColor"/>
                    <circle cx="140" cy="130" r="8" fill="currentColor"/>
                    <circle cx="100" cy="160" r="8" fill="currentColor"/>
                    <circle cx="60" cy="130" r="8" fill="currentColor"/>
                    <circle cx="40" cy="100" r="8" fill="currentColor"/>
                    <circle cx="60" cy="70" r="8" fill="currentColor"/>
                </svg>
            </div>
        </main>
    </div>
</body>
</html>

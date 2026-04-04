<?php

$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

// Header and Navigation
include __DIR__ . '/partials/header.php';

// Hero Section
include __DIR__ . '/home/hero.php';

// How It Works Section
include __DIR__ . '/home/howItWorks.php';

// Gigs Section
include __DIR__ . '/home/gigs.php';

// Why FilmGyg Section
include __DIR__ . '/home/whyFilmGig.php';

// Footer
include __DIR__ . '/partials/footer.php'; 

?>
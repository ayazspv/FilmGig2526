<?php
$pageTitle = $pageTitle ?? 'FilmGig';
$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $escape((string) $pageTitle) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8R2vk3f8mJbG3IYwYQU9f7VdAyjU2Q==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body style="background-color: #E5E7EB; color: #111827;">
    <!-- Header -->
    <header class="sticky-top shadow-sm" style="background-color: #172554;">

        <?php
            include __DIR__ . '/navbars/freelanceNavbar.php';
            include __DIR__ . '/navbars/adminNavbar.php';
            include __DIR__ . '/navbars/normalNavbar.php';
        ?>

    </header>
<?php

$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$homeApiEndpoint = $homeApiEndpoint ?? '/api/home';
$pageScripts = ['/assets/js/home.js'];

// Header and Navigation
include __DIR__ . '/partials/header.php';

?>

<main id="homeApp" data-home-api-endpoint="<?= $e($homeApiEndpoint) ?>">
	<?php include __DIR__ . '/home/hero.php'; ?>
	<?php include __DIR__ . '/home/howItWorks.php'; ?>
	<?php include __DIR__ . '/home/gigs.php'; ?>
	<?php include __DIR__ . '/home/whyFilmGig.php'; ?>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>

?>
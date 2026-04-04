<?php
$hero = $hero ?? [];
$heroTitle = (string) ($hero['title'] ?? 'Find Your Next Film Gig in Seconds!');
$heroSubtitle = (string) ($hero['subtitle'] ?? 'Browse thousands of gigs in film production, editing, and more.');
$heroCta = $hero['cta'] ?? ['url' => '/signup', 'label' => 'Click to sign up now!'];
$heroSearchPlaceholder = (string) ($hero['searchPlaceholder'] ?? 'Search for gigs');
$heroSearchButtonLabel = (string) ($hero['searchButtonLabel'] ?? 'Search');
$heroImage = $hero['image'] ?? ['src' => 'images/hero-image.png', 'alt' => 'Hero Image'];
?>
<section class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4"><?= nl2br($e($heroTitle), false) ?></h1>
            <p class="lead"><?= $e($heroSubtitle) ?></p>
            <a class="btn btn-primary btn-lg mb-3" href="<?= $e((string) ($heroCta['url'] ?? '/signup')) ?>"><?= $e((string) ($heroCta['label'] ?? 'Click to sign up now!')) ?></a>
            <div class="input-group">
                <input type="text" class="form-control" placeholder="<?= $e($heroSearchPlaceholder) ?>">
                <button class="btn btn-outline-secondary" type="button"><?= $e($heroSearchButtonLabel) ?></button>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="<?= $e((string) ($heroImage['src'] ?? 'images/hero-image.png')) ?>" class="img-fluid" alt="<?= $e((string) ($heroImage['alt'] ?? 'Hero Image')) ?>">
        </div>
    </div>
</section>

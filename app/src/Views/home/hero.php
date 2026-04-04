<?php
$hero = $hero ?? [];
$heroTitle = (string) ($hero['title'] ?? 'Find Your Next Film Gig in Seconds!');
$heroSubtitle = (string) ($hero['subtitle'] ?? 'Browse thousands of gigs in film production, editing, and more.');
$heroCta = $hero['cta'] ?? ['url' => '/signup', 'label' => 'Click to sign up now!'];
$heroSearchPlaceholder = (string) ($hero['searchPlaceholder'] ?? 'Search for gigs');
$heroSearchButtonLabel = (string) ($hero['searchButtonLabel'] ?? 'Search');
$heroImage = $hero['image'] ?? [
    'src' => 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=900&q=80',
    'alt' => 'Filmmakers collaborating on set',
];
?>
<section class="py-5" style="background-color: #E5E7EB;">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <span class="badge text-uppercase mb-3" style="background-color: #FBBF24; color: #172554;">Built for film creators</span>
                <h1 class="display-5 fw-bold" style="color: #172554;"><?= nl2br($e($heroTitle), false) ?></h1>
                <p class="lead mb-4" style="color: #1F2937;"><?= $e($heroSubtitle) ?></p>

                <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4">
                    <div class="row g-2">
                        <div class="col-12 col-md-5">
                            <input type="text" class="form-control form-control-lg" placeholder="<?= $e($heroSearchPlaceholder) ?>">
                        </div>
                        <div class="col-6 col-md-4">
                            <select class="form-select form-select-lg" aria-label="Category">
                                <option selected>Category</option>
                                <option>Production</option>
                                <option>Post-Production</option>
                                <option>Sound</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 d-grid">
                            <button class="btn btn-lg text-white" type="button" style="background-color: #B91C1C; border-color: #B91C1C;"><?= $e($heroSearchButtonLabel) ?></button>
                        </div>
                    </div>
                </div>

                <a class="btn btn-lg text-white fw-semibold" style="background-color: #172554; border-color: #172554;" href="<?= $e((string) ($heroCta['url'] ?? '/signup')) ?>"><?= $e((string) ($heroCta['label'] ?? 'Click to sign up now!')) ?></a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-12">
                        <img src="<?= $e((string) ($heroImage['src'] ?? 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=900&q=80')) ?>" class="img-fluid rounded-4 shadow" alt="<?= $e((string) ($heroImage['alt'] ?? 'Hero Image')) ?>">
                    </div>
                    <div class="col-6">
                        <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=700&q=80" class="img-fluid rounded-4 shadow-sm" alt="Camera operator on set">
                    </div>
                    <div class="col-6">
                        <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=700&q=80" class="img-fluid rounded-4 shadow-sm" alt="Film editor working">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

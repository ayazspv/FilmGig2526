<?php
$whyFilmGig = $whyFilmGig ?? [];
$whyTitle = (string) ($whyFilmGig['title'] ?? 'Why FilmGig?');
$whyDescription = (string) ($whyFilmGig['description'] ?? '');
$whyBullets = $whyFilmGig['bullets'] ?? [];
$whyImage = $whyFilmGig['image'] ?? [
    'src' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80',
    'alt' => 'Film crew working outdoors',
];
?>
<section id="why-filmgig" class="py-5" style="background-color: #1F2937;">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 text-white">
                <h2 class="fw-bold mb-3"><?= $e($whyTitle) ?></h2>
                <p class="mb-4">
                    <?= $e($whyDescription !== '' ? $whyDescription : 'FilmGig connects filmmakers, production teams, and creative specialists in one focused ecosystem. Instead of searching through generic job platforms, you discover opportunities tailored to your skills, your availability, and your career goals. Whether you are building your first portfolio project or scaling to larger productions, FilmGig helps you move from discovery to collaboration faster.') ?>
                </p>

                <div class="row g-3">
                    <?php foreach ($whyBullets as $bullet): ?>
                        <div class="col-12">
                            <div class="d-flex align-items-start gap-2 bg-white bg-opacity-10 rounded-3 p-3">
                                <i class="fa-solid fa-check mt-1" style="color: #FBBF24;"></i>
                                <span><?= $e((string) $bullet) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-12">
                        <img src="<?= $e((string) ($whyImage['src'] ?? 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1000&q=80')) ?>" class="img-fluid rounded-4 shadow" alt="<?= $e((string) ($whyImage['alt'] ?? 'Why FilmGig')) ?>">
                    </div>
                    <div class="col-6">
                        <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-sm" alt="Filmmaking collaboration">
                    </div>
                    <div class="col-6">
                        <img src="https://images.unsplash.com/photo-1478720568477-152d9b164e26?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow-sm" alt="Cinema production lighting">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
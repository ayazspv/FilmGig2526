<?php
$whyFilmGig = $whyFilmGig ?? [];
$whyTitle = (string) ($whyFilmGig['title'] ?? 'Why FilmGig?');
$whyDescription = (string) ($whyFilmGig['description'] ?? '');
$whyBullets = $whyFilmGig['bullets'] ?? [];
$whyImage = $whyFilmGig['image'] ?? ['src' => 'images/why-filmgig.png', 'alt' => 'Why FilmGig'];
?>
<section class="container my-5">
    <div class="row align-items-center">
        <div class="col-md-6">
            <img src="<?= $e((string) ($whyImage['src'] ?? 'images/why-filmgig.png')) ?>" class="img-fluid" alt="<?= $e((string) ($whyImage['alt'] ?? 'Why FilmGig')) ?>">
        </div>
        <div class="col-md-6">
            <h2><?= $e($whyTitle) ?></h2>
            <p><?= $e($whyDescription) ?></p>
            <ul class="list-group list-group-flush">
                <?php foreach ($whyBullets as $bullet): ?>
                    <li class="list-group-item"><?= $e((string) $bullet) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
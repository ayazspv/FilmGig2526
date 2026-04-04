<?php
$gigs = $gigs ?? [];
$gigsTitle = (string) ($gigs['title'] ?? 'Available Gigs');
$gigItems = $gigs['items'] ?? [];
$slides = array_chunk($gigItems, 3);
?>
<section class="container my-5">
    <h2 class="text-center mb-4"><?= $e($gigsTitle) ?></h2>
    <div id="gigsCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php if (empty($slides)): ?>
                <div class="carousel-item active">
                    <div class="row justify-content-center">
                        <article class="col-md-8">
                            <p class="text-center text-muted mb-0">No gigs available right now.</p>
                        </article>
                    </div>
                </div>
            <?php endif; ?>
            <?php foreach ($slides as $slideIndex => $slideItems): ?>
                <div class="carousel-item <?= $slideIndex === 0 ? 'active' : '' ?>">
                    <div class="row justify-content-center">
                        <?php foreach ($slideItems as $item): ?>
                            <article class="col-md-4">
                                <div class="card">
                                    <img src="<?= $e((string) ($item['thumbnail'] ?? 'thumbnail')) ?>" class="card-img-top" alt="Gig Thumbnail">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $e((string) ($item['title'] ?? 'Gig Title')) ?></h5>
                                        <p class="card-text"><?= $e((string) ($item['description'] ?? 'Short description of the gig.')) ?></p>
                                        <a href="<?= $e((string) ($item['applyUrl'] ?? 'gig-details')) ?>" class="btn btn-primary">Apply</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#gigsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#gigsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<?php
$gigs = $gigs ?? [];
$gigsTitle = (string) ($gigs['title'] ?? 'Available Gigs');
$gigItems = $gigs['items'] ?? [];
$slides = array_chunk($gigItems, 3);
?>
<section id="available-gigs" class="py-5" style="background-color: #E5E7EB;">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-2" style="color: #172554;"><?= $e($gigsTitle) ?></h2>
                <p class="mb-0" style="color: #1F2937;">Browse featured gigs automatically, or use the arrows to move through them manually.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background-color: #172554;">Newest opportunities</span>
            </div>
        </div>

        <?php if (empty($gigItems)): ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <p class="text-muted mb-0">No gigs available right now.</p>
                </div>
            </div>
        <?php else: ?>
            <div id="gigsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500" data-bs-pause="hover">
                <?php if (count($slides) > 1): ?>
                    <div class="carousel-indicators position-static mb-3">
                        <?php foreach ($slides as $slideIndex => $slideItems): ?>
                            <button type="button" data-bs-target="#gigsCarousel" data-bs-slide-to="<?= $slideIndex ?>" class="<?= $slideIndex === 0 ? 'active' : '' ?>" <?= $slideIndex === 0 ? 'aria-current="true"' : '' ?> aria-label="Slide <?= $slideIndex + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="carousel-inner">
                    <?php foreach ($slides as $slideIndex => $slideItems): ?>
                        <div class="carousel-item <?= $slideIndex === 0 ? 'active' : '' ?>">
                            <div class="row g-3 justify-content-center">
                                <?php foreach ($slideItems as $item): ?>
                                    <div class="col-12 col-lg-4 d-flex">
                                        <?php $gigItem = $item; include __DIR__ . '/gigCard.php'; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php if (!empty($gigItems) && count($slides) > 1): ?>
    <div class="container pb-5">
        <div class="d-flex justify-content-center gap-2 mt-n3">
            <button class="btn btn-outline-secondary bg-white" type="button" data-bs-target="#gigsCarousel" data-bs-slide="prev" aria-label="Previous gigs">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button class="btn btn-outline-secondary bg-white" type="button" data-bs-target="#gigsCarousel" data-bs-slide="next" aria-label="Next gigs">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
<?php endif; ?>
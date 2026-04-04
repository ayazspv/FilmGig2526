<?php
$gigs = $gigs ?? [];
$gigsTitle = (string) ($gigs['title'] ?? 'Available Gigs');
$gigItems = $gigs['items'] ?? [];
?>
<section id="available-gigs" class="py-5" style="background-color: #E5E7EB;">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4">
            <div>
                <h2 class="fw-bold mb-2" style="color: #172554;"><?= $e($gigsTitle) ?></h2>
                <p class="mb-0" style="color: #1F2937;">Scroll horizontally to explore opportunities available right now.</p>
            </div>
            <span class="badge mt-3 mt-md-0" style="background-color: #172554;">Newest opportunities</span>
        </div>

        <?php if (empty($gigItems)): ?>
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <p class="text-muted mb-0">No gigs available right now.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex gap-3 overflow-auto pb-2 gigs-scroll-row">
                <?php foreach ($gigItems as $item): ?>
                    <article class="card border-0 shadow-sm rounded-4 flex-shrink-0 gig-scroll-card">
                        <img src="<?= $e((string) ($item['thumbnail'] ?? 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=80')) ?>" class="card-img-top rounded-top-4" alt="<?= $e((string) ($item['title'] ?? 'Gig Thumbnail')) ?>">
                        <div class="card-body d-flex flex-column">
                            <h3 class="h5 card-title fw-bold mb-2"><?= $e((string) ($item['title'] ?? 'Gig Title')) ?></h3>
                            <p class="card-text" style="color: #1F2937;"><?= $e((string) ($item['description'] ?? 'Short description of the gig.')) ?></p>
                            <div class="mt-auto d-flex justify-content-between align-items-center pt-2">
                                <span class="fw-bold" style="color: #B91C1C;"><?= $e((string) ($item['price'] ?? '$250/day')) ?></span>
                                <a href="<?= $e((string) ($item['applyUrl'] ?? 'gig-details')) ?>" class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
$gigItem = $gigItem ?? [];
?>
<article class="card border-0 shadow-sm rounded-4 w-100 overflow-hidden">
    <img src="<?= $e((string) ($gigItem['thumbnail'] ?? 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=900&q=80')) ?>" class="card-img-top gigs-thumbnail" alt="<?= $e((string) ($gigItem['title'] ?? 'Gig Thumbnail')) ?>">
    <div class="card-body d-flex flex-column">
        <h3 class="h5 card-title fw-bold mb-2"><?= $e((string) ($gigItem['title'] ?? 'Gig Title')) ?></h3>
        <p class="card-text flex-grow-1" style="color: #1F2937;"><?= $e((string) ($gigItem['description'] ?? 'Short description of the gig.')) ?></p>
        <div class="mt-auto d-flex justify-content-between align-items-center pt-2">
            <span class="fw-bold" style="color: #B91C1C;"><?= $e((string) ($gigItem['price'] ?? '$250/day')) ?></span>
            <a href="<?= $e((string) ($gigItem['applyUrl'] ?? 'gig-details')) ?>" class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
        </div>
    </div>
</article>
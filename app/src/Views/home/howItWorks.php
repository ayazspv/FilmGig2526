<?php
$howItWorks = $howItWorks ?? [];
$howItWorksTitle = (string) ($howItWorks['title'] ?? 'This is How it Works');
$steps = $howItWorks['steps'] ?? [];
?>
<section id="how-it-works" class="py-5" style="background-color: #ffffff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color: #172554;"><?= $e($howItWorksTitle) ?></h2>
            <p class="mb-0" style="color: #1F2937;">From discovery to getting hired, move through the flow in minutes.</p>
        </div>

        <div class="row g-3 g-md-2 align-items-center justify-content-center">
            <?php foreach ($steps as $index => $step): ?>
                <div class="col-12 col-md-2 d-flex">
                    <article class="card border-0 shadow-sm rounded-4 w-100 text-center p-3">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                            style="width: 70px; height: 70px; background-color: #E5E7EB; color: #172554;">
                            <i class="<?= $e((string) ($step['icon'] ?? 'fa-solid fa-circle')) ?> fs-4"></i>
                        </div>
                        <h3 class="h5 fw-bold"><?= $e((string) ($step['title'] ?? 'Step')) ?></h3>
                        <p class="mb-0" style="color: #1F2937;"><?= $e((string) ($step['description'] ?? '')) ?></p>
                    </article>
                </div>

                <?php if ($index < count($steps) - 1): ?>
                    <div class="col-12 d-md-none text-center py-1">
                        <i class="fa-solid fa-arrow-down fs-4" style="color: #B91C1C;"></i>
                    </div>
                    <div class="col-md-1 d-none d-md-flex justify-content-center align-items-center">
                        <i class="fa-solid fa-arrow-right fs-5" style="color: #B91C1C;"></i>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
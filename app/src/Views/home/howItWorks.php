<?php
$howItWorks = $howItWorks ?? [];
$howItWorksTitle = (string) ($howItWorks['title'] ?? 'This is How it Works');
$steps = $howItWorks['steps'] ?? [];
?>
<section class="container my-5">
    <h2 class="text-center mb-4"><?= $e($howItWorksTitle) ?></h2>
    <div class="row text-center">
        <?php foreach ($steps as $step): ?>
            <div class="col-md-3">
                <i class="<?= $e((string) ($step['icon'] ?? 'bi bi-circle fs-1')) ?> fs-1"></i>
                <h3><?= $e((string) ($step['title'] ?? 'Step')) ?></h3>
                <p><?= $e((string) ($step['description'] ?? '')) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>
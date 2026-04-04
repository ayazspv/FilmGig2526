<?php
$pageTitle = $pageTitle ?? 'Welcome - FilmGig';
$headline = $headline ?? 'Welcome to FilmGig';
$subtitle = $subtitle ?? 'Your Film Event Management Platform';
$description = $description ?? '';
$features = $features ?? [];
$actions = $actions ?? [];

$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

include __DIR__ . '/partials/header.php';
?>
<div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
    <main class="row w-100 g-4 align-items-center">
        <div class="col-lg-6 px-4">
            <h1 class="display-2 fw-bold mb-3 text-primary"><?= $e($headline) ?></h1>
            <p class="lead text-secondary mb-2"><?= $e($subtitle) ?></p>
            <p class="text-muted mb-4 lh-lg"><?= $e($description) ?></p>

            <div class="mb-4">
                <h5 class="fw-bold mb-3">Testing Information:</h5>
                <ul class="list-unstyled">
                    <?php foreach ($features as $feature): ?>
                        <li class="mb-2"><span class="text-success">✓</span> <?= $e($feature) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <?php foreach ($actions as $action): ?>
                    <a href="<?= $e($action['url']) ?>" class="<?= $e($action['class']) ?>"><?= $e($action['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-6 d-flex justify-content-center align-items-center px-4">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="text-primary" style="width: 200px; height: 200px;">
                <!-- Film reel icon -->
                <circle cx="100" cy="100" r="90" fill="none" stroke="currentColor" stroke-width="4"/>
                <circle cx="100" cy="100" r="60" fill="none" stroke="currentColor" stroke-width="4"/>
                <circle cx="100" cy="100" r="30" fill="none" stroke="currentColor" stroke-width="4"/>

                <!-- Film holes -->
                <circle cx="100" cy="40" r="8" fill="currentColor"/>
                <circle cx="140" cy="70" r="8" fill="currentColor"/>
                <circle cx="160" cy="100" r="8" fill="currentColor"/>
                <circle cx="140" cy="130" r="8" fill="currentColor"/>
                <circle cx="100" cy="160" r="8" fill="currentColor"/>
                <circle cx="60" cy="130" r="8" fill="currentColor"/>
                <circle cx="40" cy="100" r="8" fill="currentColor"/>
                <circle cx="60" cy="70" r="8" fill="currentColor"/>
            </svg>
        </div>
    </main>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>

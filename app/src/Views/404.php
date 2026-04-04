<?php
$pageTitle = $pageTitle ?? '404 - Page Not Found';
$statusCode = $statusCode ?? 404;
$errorTitle = $errorTitle ?? 'Page Not Found';
$errorMessage = $errorMessage ?? 'Sorry, the page you\'re looking for doesn\'t exist or has been moved.';
$actions = $actions ?? [
    ['url' => '/', 'label' => 'Go Back Home', 'class' => 'btn btn-primary'],
    ['url' => 'javascript:history.back()', 'label' => 'Go Back', 'class' => 'btn btn-secondary'],
];

$e = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

include __DIR__ . '/partials/header.php';
?>
<div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
    <main class="row w-100 g-4 align-items-center">
        <div class="col-lg-6 text-center px-4">
            <h1 class="display-1 fw-bold text-danger mb-3"><?= $e((string) $statusCode) ?></h1>
            <h2 class="h3 fw-bold mb-3 text-dark"><?= $e($errorTitle) ?></h2>
            <p class="lead text-muted mb-4"><?= $e($errorMessage) ?></p>
            <div class="d-flex gap-2 justify-content-center flex-wrap">
                <?php foreach ($actions as $action): ?>
                    <a href="<?= $e($action['url']) ?>" class="<?= $e($action['class']) ?>"><?= $e($action['label']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-6 d-flex justify-content-center align-items-center px-4">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="text-danger" style="width: 200px; height: 200px;">
                <!-- Magnifying glass icon -->
                <circle cx="70" cy="70" r="50" fill="none" stroke="currentColor" stroke-width="8"/>
                <line x1="110" y1="110" x2="160" y2="160" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
                <text x="50" y="85" font-size="40" font-weight="bold" text-anchor="middle" fill="currentColor">?</text>
            </svg>
        </div>
    </main>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>

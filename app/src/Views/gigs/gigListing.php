<?php
$pageTitle = $viewModel->pageTitle;
$gigsListingScriptVersion = (string) (@filemtime(__DIR__ . '/../../../public/assets/js/gigsListing.js') ?: time());
$pageScripts = ['/assets/js/gigsListing.js?v=' . $gigsListingScriptVersion];
$filters = $viewModel->filters;
$gigs = $viewModel->gigs;
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm"
                style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($viewModel->badgeLabel) ?></span>
                <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($viewModel->heroTitle) ?></h1>
                <p class="mb-0"><?= htmlspecialchars($viewModel->heroDescription) ?></p>
            </section>

            <section class="row g-4">
                <aside class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Filters</h2>
                            <div>
                                <div class="mb-3">
                                    <label for="gigSearch" class="form-label fw-semibold">Search by Gig Name</label>
                                    <input type="search" id="gigSearch" name="search" class="form-control"
                                        value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="e.g. Camera Operator">
                                </div>

                                <div class="mb-3">
                                    <label for="gigDate" class="form-label fw-semibold">Date</label>
                                    <input type="date" id="gigDate" name="date" class="form-control" value="<?= htmlspecialchars($filters['date']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="gigLocation" class="form-label fw-semibold">Location</label>
                                    <input type="text" id="gigLocation" name="location" class="form-control"
                                        value="<?= htmlspecialchars($filters['location']) ?>" placeholder="City or address">
                                </div>

                                <div class="mb-3">
                                    <label for="gigHourlyRate" class="form-label fw-semibold">Minimum Hourly Rate</label>
                                    <input type="range" id="gigHourlyRate" name="minimumRate" class="form-range" min="20" max="100" step="5"
                                        value="<?= htmlspecialchars((string) $filters['minimumRate']) ?>" oninput="document.getElementById('rateValue').innerText = this.value">
                                    <span id="rateValue" class="fw-bold"><?= htmlspecialchars((string) $filters['minimumRate']) ?></span> EUR/hr
                                </div>

                                <fieldset class="mb-4">
                                    <legend class="form-label fw-semibold">Categories</legend>
                                    <?php foreach ($viewModel->categoryOptions as $value => $label): ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="category-<?= htmlspecialchars($value) ?>" value="<?= htmlspecialchars($value) ?>"
                                                <?= in_array($value, $viewModel->selectedCategories, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="category-<?= htmlspecialchars($value) ?>">
                                                <?= htmlspecialchars($label) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </aside>

                <section class="col-12 col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 fw-bold mb-0" style="color: #172554;">Available Gigs</h2>
                        <span class="badge" id="gigsResultCount" style="background-color: #172554;">0 results</span>
                    </div>

                    <div class="row g-4" id="gigContainer" data-gigs-api-endpoint="/api/gigs">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body text-center py-5">
                                    <p class="text-muted mb-0">Loading gigs...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
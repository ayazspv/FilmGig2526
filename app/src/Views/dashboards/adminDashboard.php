<?php
$dashboardApiEndpoint = $dashboardApiEndpoint ?? '/api/dashboard';
$dashboardKind = $dashboardKind ?? 'productionHouse';
$pageScripts = ['/assets/js/dashboard.js'];
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main class="flex-grow-1 py-5" data-dashboard-root data-dashboard-api-endpoint="<?= htmlspecialchars($dashboardApiEndpoint) ?>" data-dashboard-kind="<?= htmlspecialchars($dashboardKind) ?>" data-dashboard-user-name="<?= htmlspecialchars((string) ($_SESSION['name'] ?? 'User')) ?>">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm" style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;" data-dashboard-badge>Loading dashboard...</span>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                    <div>
                        <h1 class="display-6 fw-bold mb-2" data-dashboard-hero-title>Loading dashboard...</h1>
                        <p class="mb-0" data-dashboard-hero-description>Fetching live dashboard data...</p>
                    </div>
                    <a href="/dashboard/submissions/received" class="btn btn-warning fw-semibold d-none" style="color: #172554;" data-dashboard-review-link>Review Submissions</a>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3" data-dashboard-stats>
                    <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4 text-center h-100"><div class="card-body"><div class="placeholder-glow"><div class="placeholder col-7 mb-2"></div><div class="placeholder col-5"></div></div></div></div></div>
                    <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4 text-center h-100"><div class="card-body"><div class="placeholder-glow"><div class="placeholder col-7 mb-2"></div><div class="placeholder col-5"></div></div></div></div></div>
                    <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4 text-center h-100"><div class="card-body"><div class="placeholder-glow"><div class="placeholder col-7 mb-2"></div><div class="placeholder col-5"></div></div></div></div></div>
                    <div class="col-12 col-sm-6 col-xl-3"><div class="card border-0 shadow-sm rounded-4 text-center h-100"><div class="card-body"><div class="placeholder-glow"><div class="placeholder col-7 mb-2"></div><div class="placeholder col-5"></div></div></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-3" style="color: #172554;" data-dashboard-primary-title>Loading gigs...</h2>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead data-dashboard-primary-head></thead>
                                        <tbody data-dashboard-primary-body></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-3" style="color: #172554;" data-dashboard-secondary-title>Loading applications...</h2>
                                <ul class="list-group list-group-flush" data-dashboard-secondary-list></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
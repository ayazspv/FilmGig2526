<?php
$dashboardStats = $viewModel->stats;
$pageTitle = $viewModel->pageTitle;
$badgeLabel = $viewModel->badgeLabel;
$heroTitle = $viewModel->heroTitle;
$heroDescription = $viewModel->heroDescription;
$primaryTable = $viewModel->primaryTable;
$successMessage = $_SESSION['gig_success_message'] ?? null;
unset($_SESSION['gig_success_message']);
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm" style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($badgeLabel) ?></span>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                    <div>
                        <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($heroTitle) ?></h1>
                        <p class="mb-0"><?= htmlspecialchars($heroDescription) ?></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/dashboard/gigs/new" class="btn btn-warning fw-semibold" style="color: #172554;">Add Gig</a>
                        <a href="/dashboard" class="btn btn-outline-light fw-semibold">Back to Dashboard</a>
                    </div>
                </div>
            </section>

            <?php if ($successMessage !== null): ?>
                <div class="alert alert-success shadow-sm border-0 rounded-4 mb-4">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <section class="mb-4">
                <div class="row g-3">
                    <?php foreach ($dashboardStats as $stat): ?>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                                <div class="card-body">
                                    <p class="small text-uppercase mb-2" style="color: #1F2937;"><?= htmlspecialchars($stat['label']) ?></p>
                                    <p class="h3 mb-1" style="color: #172554;"><?= htmlspecialchars($stat['value']) ?></p>
                                    <p class="small mb-0" style="color: #B91C1C;"><?= htmlspecialchars($stat['note']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h2 class="h4 fw-bold mb-3" style="color: #172554;"><?= htmlspecialchars($primaryTable['title']) ?></h2>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <?php foreach ($primaryTable['columns'] as $column): ?>
                                            <th><?= htmlspecialchars($column) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($primaryTable['rows'])): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                No gigs found for this account yet.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($primaryTable['rows'] as $row): ?>
                                            <?php
                                                $statusLabel = (string) ($row['status'] ?? 'Unknown');
                                                $normalizedStatus = strtolower($statusLabel);
                                                $statusStyle = match ($normalizedStatus) {
                                                    'active' => 'background-color: #15803D; color: #FFFFFF;',
                                                    'closed' => 'background-color: #B91C1C; color: #FFFFFF;',
                                                    default => 'background-color: #6B7280; color: #FFFFFF;',
                                                };
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['title']) ?></td>
                                                <td><?= htmlspecialchars($row['category']) ?></td>
                                                <td>
                                                    <span class="badge" style="<?= htmlspecialchars($statusStyle) ?>">
                                                        <?= htmlspecialchars($statusLabel) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars((string) $row['value']) ?></td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <a href="<?= htmlspecialchars($row['viewUrl']) ?>" class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View</a>
                                                        <a href="<?= htmlspecialchars($row['editUrl']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../../partials/footer.php'; ?>
</div>
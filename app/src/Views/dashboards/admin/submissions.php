<?php
$pageTitle = $viewModel->pageTitle;
$stats = $viewModel->stats;
$submissions = $viewModel->submissions;
$successMessage = $successMessage ?? null;
$errorMessage = $errorMessage ?? null;
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm" style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($viewModel->badgeLabel) ?></span>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
                    <div>
                        <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($viewModel->heroTitle) ?></h1>
                        <p class="mb-0"><?= htmlspecialchars($viewModel->heroDescription) ?></p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/dashboard" class="btn btn-outline-light fw-semibold">Back to Dashboard</a>
                    </div>
                </div>
            </section>

            <?php if ($successMessage !== null): ?>
                <div class="alert alert-success shadow-sm border-0 rounded-4 mb-4"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== null): ?>
                <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4"><?= htmlspecialchars($errorMessage) ?></div>
            <?php endif; ?>

            <section class="mb-4">
                <div class="row g-3">
                    <?php foreach ($stats as $stat): ?>
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
                        <h2 class="h4 fw-bold mb-3" style="color: #172554;"><?= htmlspecialchars($viewModel->tableTitle) ?></h2>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Gig</th>
                                        <th>Freelancer</th>
                                        <th>Submission Status</th>
                                        <th>Gig Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($submissions)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted"><?= htmlspecialchars($viewModel->emptyStateMessage) ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold"><?= htmlspecialchars($submission['gigTitle']) ?></div>
                                                    <small class="text-muted">#<?= htmlspecialchars((string) $submission['gigId']) ?></small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?= htmlspecialchars($submission['freelancerName']) ?></div>
                                                    <?php if (!empty($submission['freelancerEmail'])): ?>
                                                        <small class="text-muted"><?= htmlspecialchars($submission['freelancerEmail']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $submission['status'] === 'pending' ? 'text-bg-warning' : ($submission['status'] === 'accepted' ? 'text-bg-success' : 'text-bg-secondary') ?>">
                                                        <?= htmlspecialchars($submission['statusLabel']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $submission['gigStatus'] === 'closed' ? 'text-bg-secondary' : 'text-bg-primary' ?>">
                                                        <?= htmlspecialchars($submission['gigStatusLabel']) ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($submission['submittedAt']) ?></td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <a href="<?= htmlspecialchars($submission['detailUrl']) ?>" class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
                                                        <?php if (!empty($submission['freelancerProfileUrl'])): ?>
                                                            <a href="<?= htmlspecialchars($submission['freelancerProfileUrl']) ?>" class="btn btn-sm btn-outline-secondary">View Freelancer Profile</a>
                                                        <?php endif; ?>
                                                        <?php if ($submission['canReview']): ?>
                                                            <form method="POST" action="/dashboard/submissions/<?= htmlspecialchars((string) $submission['submissionId']) ?>/review" class="d-inline d-flex flex-wrap gap-2">
                                                                <button type="submit" name="decision" value="accepted" class="btn btn-sm btn-success">Accept</button>
                                                                <button type="submit" name="decision" value="rejected" class="btn btn-sm btn-outline-danger">Reject</button>
                                                            </form>
                                                        <?php endif; ?>
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
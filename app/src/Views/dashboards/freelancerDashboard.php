<?php
$dashboardStats = $viewModel->stats;
$applications = $viewModel->applications;
$recommendedGigs = $viewModel->recommendedGigs;
$recentActions = $viewModel->recentActions;
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <!-- Welcome Banner -->
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm"
                style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3"
                    style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($viewModel->badgeLabel) ?></span>
                <h1 class="display-6 fw-bold mb-2">Welcome,
                    <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?></h1>
                <p class="mb-0"><?= htmlspecialchars($viewModel->heroDescription) ?></p>
            </section>

            <!-- Dashboard Status -->
            <section class="mb-4">
                <div class="row g-3">
                    <?php foreach ($dashboardStats as $stat): ?>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                                <div class="card-body">
                                    <p class="small text-uppercase mb-2" style="color: #1F2937;">
                                        <?= htmlspecialchars($stat['label']) ?></p>
                                    <p class="h3 mb-1" style="color: #172554;" <?= isset($stat['id']) ? ' id="' . htmlspecialchars((string) $stat['id']) . '"' : '' ?>>
                                        <?= htmlspecialchars($stat['value']) ?></p>
                                    <p class="small mb-0" style="color: #B91C1C;"><?= htmlspecialchars($stat['note']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- My Gigs -->
            <section class="mb-4 col-12 w-100">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                    <h2 class="h4 fw-bold mb-0" style="color: #172554;">
                                        <?= htmlspecialchars($applications['title']) ?>
                                    </h2>
                                    <a href="/dashboard/submissions" class="btn btn-sm btn-outline-secondary">Open Submissions</a>
                                </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <?php foreach ($applications['columns'] as $column): ?>
                                            <th>
                                                <?= htmlspecialchars($column) ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody id="recommendedGigsTableBody">
                                    <?php foreach ($applications['rows'] as $row): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($row['title']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['status']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['payType']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row['payRate']) ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-secondary">View
                                                    Details</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-4">
                    <!-- Recommended Gigs List -->
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-3" style="color: #172554;">
                                    <?= htmlspecialchars($recommendedGigs['title']) ?></h2>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <?php foreach ($recommendedGigs['columns'] as $column): ?>
                                                    <th><?= htmlspecialchars($column) ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody id="recommendedGigsTableBody">
                                            <?php foreach ($recommendedGigs['rows'] as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                                    <td><?= htmlspecialchars($row['status']) ?></td>
                                                    <td><?= htmlspecialchars($row['value']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm text-white"
                                                            style="background-color: #172554; border-color: #172554;">Apply</button>
                                                        <button class="btn btn-sm btn-outline-secondary">View
                                                            Details</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Application Updates -->
                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-3" style="color: #172554;">
                                    <?= htmlspecialchars($recentActions['title']) ?></h2>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($recentActions['items'] as $item): ?>
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($item['title']) ?></p>
                                                    <small
                                                        style="color: #1F2937;"><?= htmlspecialchars($item['subtitle']) ?></small>
                                                </div>
                                                <small class="text-muted"><?= htmlspecialchars($item['meta']) ?></small>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
<?php
$dashboardStats = $viewModel->stats;
$primaryTable = $viewModel->primaryTable;
$secondaryList = $viewModel->secondaryList;
$focus = $viewModel->focus;
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm"
                style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($viewModel->badgeLabel) ?></span>
                <h1 class="display-6 fw-bold mb-2">Welcome, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?></h1>
                <p class="mb-0"><?= htmlspecialchars($viewModel->heroDescription) ?></p>
            </section>

            <section class="mb-5">
                <div class="row g-3">
                    <?php foreach ($dashboardStats as $stat): ?>
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="card border-0 shadow-sm rounded-4 text-center h-100">
                                <div class="card-body">
                                    <p class="small text-uppercase mb-2" style="color: #1F2937;"><?= htmlspecialchars($stat['label']) ?></p>
                                    <p class="h3 mb-1" style="color: #172554;"<?= isset($stat['id']) ? ' id="' . htmlspecialchars((string) $stat['id']) . '"' : '' ?>><?= htmlspecialchars($stat['value']) ?></p>
                                    <p class="small mb-0" style="color: #B91C1C;"><?= htmlspecialchars($stat['note']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="mb-5">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
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
                                            <?php foreach ($primaryTable['rows'] as $row): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                                    <td>
                                                        <span class="badge" style="background-color: #FBBF24; color: #172554;">
                                                            <?= htmlspecialchars($row['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars((string) $row['value']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View</button>
                                                        <button class="btn btn-sm btn-outline-secondary">Edit</button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-3" style="color: #172554;"><?= htmlspecialchars($secondaryList['title']) ?></h2>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($secondaryList['items'] as $item): ?>
                                        <li class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div>
                                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($item['title']) ?></p>
                                                    <small style="color: #1F2937;"><?= htmlspecialchars($item['subtitle']) ?></small>
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

            <section>
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-2" style="color: #172554;"><?= htmlspecialchars($focus['leftTitle']) ?></h2>
                                <p class="mb-3" style="color: #1F2937;"><?= htmlspecialchars($focus['leftDescription']) ?></p>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($focus['leftItems'] as $item): ?>
                                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong><?= htmlspecialchars($item['title']) ?></strong>
                                                <small class="d-block" style="color: #1F2937;"><?= htmlspecialchars($item['subtitle']) ?></small>
                                            </span>
                                            <span class="badge" style="background-color: #172554;"><?= htmlspecialchars($item['badge']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <h2 class="h4 fw-bold mb-2" style="color: #172554;"><?= htmlspecialchars($focus['rightTitle']) ?></h2>
                                <p class="mb-3" style="color: #1F2937;"><?= htmlspecialchars($focus['rightDescription']) ?></p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php foreach ($focus['badges'] as $badge): ?>
                                        <span class="badge" style="background-color: #172554;"><?= htmlspecialchars($badge) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <button class="btn text-white" style="background-color: #B91C1C; border-color: #B91C1C;"><?= htmlspecialchars($focus['buttonLabel']) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
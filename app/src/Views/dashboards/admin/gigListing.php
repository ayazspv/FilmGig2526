<?php
$pageTitle = $viewModel->pageTitle;
$primaryTable = $viewModel->primaryTable;
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <div class="d-flex justify-content-end mb-3">
                <a href="/dashboard" class="btn btn-outline-secondary fw-semibold">Back to Dashboard</a>
            </div>

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
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../../partials/footer.php'; ?>
</div>
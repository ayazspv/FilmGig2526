<?php
$viewModel = $viewModel ?? \App\ViewModels\AdminGigEditingViewModel::createDefault();
$pageTitle = $viewModel->pageTitle;
$formFields = $viewModel->formFields;
$gigData = $viewModel->gigData;
$errors = $viewModel->errors ?? [];
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm"
                style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                    <div>
                        <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($viewModel->badgeLabel) ?></span>
                        <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($viewModel->heroTitle) ?></h1>
                        <p class="mb-0"><?= htmlspecialchars($viewModel->heroDescription) ?></p>
                    </div>

                    <a href="/dashboard" class="btn btn-outline-light fw-semibold align-self-md-start">Back to Dashboard</a>
                </div>
            </section>

            <section class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <p class="fw-semibold mb-2">Please fix the highlighted issues.</p>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($gigData['imageUrl'])): ?>
                        <div class="mb-4">
                            <p class="fw-semibold mb-2" style="color: #172554;">Current Gig Picture</p>
                            <img src="<?= htmlspecialchars($gigData['imageUrl']) ?>" alt="<?= htmlspecialchars($gigData['title']) ?>" class="img-fluid rounded-4 shadow-sm" style="max-height: 240px; object-fit: cover;">
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-4">
                            <?php foreach ($formFields as $field): ?>
                                <div class="col-12 <?= $field['type'] === 'textarea' ? 'col-lg-12' : 'col-lg-6' ?>">
                                    <label for="<?= htmlspecialchars($field['name']) ?>" class="form-label fw-semibold"><?= htmlspecialchars($field['label']) ?></label>

                                    <?php if ($field['type'] === 'file'): ?>
                                        <input class="form-control form-control-lg" id="<?= htmlspecialchars($field['name']) ?>" name="<?= htmlspecialchars($field['name']) ?>" type="file" accept="image/*">
                                        <?php if (!empty($field['help'])): ?>
                                            <div class="form-text"><?= htmlspecialchars($field['help']) ?></div>
                                        <?php endif; ?>
                                    <?php elseif ($field['type'] === 'select'): ?>
                                        <select class="form-select form-select-lg" id="<?= htmlspecialchars($field['name']) ?>" name="<?= htmlspecialchars($field['name']) ?>" required>
                                            <?php foreach ($field['options'] as $optionValue => $optionLabel): ?>
                                                <option value="<?= htmlspecialchars((string) $optionValue) ?>"<?= (($gigData[$field['name']] ?? '') === $optionValue) ? ' selected' : '' ?>>
                                                    <?= htmlspecialchars((string) $optionLabel) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($field['type'] === 'textarea'): ?>
                                        <textarea class="form-control" id="<?= htmlspecialchars($field['name']) ?>" name="<?= htmlspecialchars($field['name']) ?>"
                                            rows="5" placeholder="<?= htmlspecialchars($field['placeholder'] ?? '') ?>" required><?= htmlspecialchars($gigData[$field['name']] ?? '') ?></textarea>
                                    <?php else: ?>
                                        <input class="form-control form-control-lg" id="<?= htmlspecialchars($field['name']) ?>" name="<?= htmlspecialchars($field['name']) ?>"
                                            type="<?= htmlspecialchars($field['type']) ?>" placeholder="<?= htmlspecialchars((string) ($field['placeholder'] ?? '')) ?>"
                                            value="<?= htmlspecialchars($gigData[$field['name']] ?? '') ?>" required>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4 justify-content-end">
                            <button type="submit" class="btn btn-lg text-white" style="background-color: #172554; border-color: #172554;">Save Changes</button>
                            <button type="submit" class="btn btn-lg text-white" style="background-color: #B91C1C; border-color: #B91C1C;" formaction="/dashboard/gigs/<?= htmlspecialchars((string) $gigData['gigId']) ?>/delete" formmethod="POST" formnovalidate onclick="return confirm('Are you sure you want to delete this gig?');">Delete Gig</button>
                            <a href="/dashboard/gigs" class="btn btn-lg btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../../partials/footer.php'; ?>
</div>
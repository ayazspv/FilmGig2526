<?php
$pageTitle = $viewModel->pageTitle;
$gig = $viewModel->gig;
$contact = $viewModel->contact;
$metadata = $viewModel->metadata;
$currentRole = (string) ($_SESSION['auth_user_role'] ?? '');
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

            <section class="row g-4 mb-4">
                <aside class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <img src="<?= htmlspecialchars($gig['imageUrl']) ?>" class="card-img-top rounded-top-4" alt="<?= htmlspecialchars($gig['title']) ?>" style="height: 320px; object-fit: cover;">
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <span class="badge" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($gig['category']) ?></span>
                                <span class="badge text-bg-secondary"><?= htmlspecialchars($gig['status']) ?></span>
                            </div>
                            <h2 class="h4 fw-bold mb-3" style="color: #172554;"><?= htmlspecialchars($gig['title']) ?></h2>
                            <p class="mb-0" style="color: #1F2937;"><?= htmlspecialchars($gig['description']) ?></p>
                        </div>
                    </div>
                </aside>

                <section class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Gig Details</h2>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($metadata as $item): ?>
                                    <li class="list-group-item px-0" style="background-color: transparent;"><strong><?= htmlspecialchars($item['label']) ?>:</strong> <?= htmlspecialchars($item['value']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Production Contact</h2>
                            <p class="mb-2"><strong>Name:</strong> <?= htmlspecialchars($contact['name']) ?></p>
                            <p class="mb-2"><strong>Company:</strong> <?= htmlspecialchars($contact['company']) ?></p>
                            <?php if (!empty($contact['website'])): ?>
                                <p class="mb-2"><strong>Website:</strong> <a href="<?= htmlspecialchars($contact['website']) ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($contact['website']) ?></a></p>
                            <?php endif; ?>
                            <?php if (!empty($contact['email'])): ?>
                                <p class="mb-0"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($contact['email']) ?>"><?= htmlspecialchars($contact['email']) ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </section>

            <?php if ($currentRole === 'freelancer'): ?>
                <footer class="text-center">
                    <button class="btn btn-lg text-white px-4" style="background-color: #B91C1C; border-color: #B91C1C;">Apply for This Gig</button>
                </footer>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
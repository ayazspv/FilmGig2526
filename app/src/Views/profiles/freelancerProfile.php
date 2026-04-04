<?php
$pageTitle = $viewModel->pageTitle;
$summary = $viewModel->summary;
$contact = $viewModel->contact;
$personal = $viewModel->personal;
$niches = $viewModel->nichePreferences;
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
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <div class="rounded-circle overflow-hidden mx-auto mb-3 border border-3"
                                style="width: 150px; height: 150px; border-color: #E5E7EB !important;">
                                <img src="<?= htmlspecialchars($viewModel->profileImage) ?>"
                                    alt="<?= htmlspecialchars($summary['name']) ?>"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <h2 class="h4 fw-bold mb-1" style="color: #172554;"><?= htmlspecialchars($summary['name']) ?></h2>
                            <p class="mb-1" style="color: #1F2937;"><strong>Username:</strong> <?= htmlspecialchars($summary['username']) ?></p>
                            <p class="mb-0" style="color: #1F2937;"><strong>Role:</strong> <?= htmlspecialchars($summary['role']) ?></p>
                        </div>
                    </div>
                </aside>

                <section class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Contact Information</h2>
                            <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($contact['email']) ?></p>
                            <p class="mb-2"><strong>Phone:</strong> <?= htmlspecialchars($contact['phone']) ?></p>
                            <p class="mb-0"><strong>Address:</strong> <?= htmlspecialchars($contact['address']) ?></p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Personal Information</h2>
                            <p class="mb-2"><strong>Date of Birth:</strong> <?= htmlspecialchars($personal['dateOfBirth']) ?></p>
                            <p class="mb-0" style="color: #1F2937;"><strong>Bio:</strong> <?= htmlspecialchars($personal['bio']) ?></p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Niche Preferences</h2>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($niches as $niche): ?>
                                    <span class="badge" style="background-color: #172554;"><?= htmlspecialchars($niche) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
<?php
$pageTitle = $viewModel->pageTitle;
$gig = $viewModel->gig;
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
                        <img src="<?= htmlspecialchars($gig['image']) ?>" class="card-img-top rounded-top-4" alt="<?= htmlspecialchars($gig['title']) ?>"
                            style="height: 320px; object-fit: cover;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($gig['category']) ?></span>
                                <span class="fw-semibold" style="color: #172554;"><?= htmlspecialchars($gig['payRate']) ?></span>
                            </div>
                            <h2 class="h4 fw-bold mb-2" style="color: #172554;"><?= htmlspecialchars($gig['title']) ?></h2>
                            <p class="mb-0" style="color: #1F2937;"><?= htmlspecialchars($gig['description']) ?></p>
                        </div>
                    </div>
                </aside>

                <section class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Gig Details</h2>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0" style="background-color: transparent;"><strong>Rate Type:</strong> <?= htmlspecialchars($gig['rateType']) ?></li>
                                <li class="list-group-item px-0" style="background-color: transparent;"><strong>Pay Rate:</strong> <?= htmlspecialchars($gig['payRate']) ?></li>
                                <li class="list-group-item px-0" style="background-color: transparent;"><strong>Duration:</strong> <?= htmlspecialchars($gig['duration']) ?></li>
                                <li class="list-group-item px-0" style="background-color: transparent;"><strong>Location:</strong> <?= htmlspecialchars($gig['location']) ?></li>
                                <li class="list-group-item px-0" style="background-color: transparent;"><strong>Start Date:</strong> <?= htmlspecialchars($gig['startDate']) ?></li>
                                <li class="list-group-item px-0" style="background-color: transparent;"><strong>Start Time:</strong> <?= htmlspecialchars($gig['startTime']) ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Production Contact</h2>
                            <p class="mb-2"><strong>Name:</strong> <?= htmlspecialchars($viewModel->contact['name']) ?></p>
                            <p class="mb-2"><strong>Company:</strong> <?= htmlspecialchars($viewModel->contact['company']) ?></p>
                            <p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($viewModel->contact['email']) ?></p>
                        </div>
                    </div>
                </section>
            </section>

            <section class="row g-4 mb-4">
                <section class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Requirements</h2>
                            <ul class="mb-0" style="color: #1F2937;">
                                <?php foreach ($viewModel->requirements as $requirement): ?>
                                    <li class="mb-2"><?= htmlspecialchars($requirement) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="col-12 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">What You Get</h2>
                            <ul class="mb-0" style="color: #1F2937;">
                                <?php foreach ($viewModel->highlights as $highlight): ?>
                                    <li class="mb-2"><?= htmlspecialchars($highlight) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </section>
            </section>

            <!-- <section class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0" style="color: #172554;">Related Gigs</h2>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($viewModel->relatedGigs as $relatedGig): ?>
                            <article class="col-12 col-md-6">
                                <div class="border rounded-3 p-3 h-100" style="border-color: #E5E7EB !important;">
                                    <h3 class="h6 fw-bold mb-1" style="color: #172554;"><?= htmlspecialchars($relatedGig['title']) ?></h3>
                                    <p class="mb-1 small" style="color: #1F2937;"><?= htmlspecialchars($relatedGig['location']) ?></p>
                                    <p class="mb-3 small fw-semibold" style="color: #172554;"><?= htmlspecialchars($relatedGig['rate']) ?></p>
                                    <a href="<?= htmlspecialchars($relatedGig['url']) ?>" class="btn btn-sm text-white" style="background-color: #172554; border-color: #172554;">View Gig</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section> -->

            <footer class="text-center">
                <button class="btn btn-lg text-white px-4" style="background-color: #B91C1C; border-color: #B91C1C;">Apply for This Gig</button>
            </footer>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
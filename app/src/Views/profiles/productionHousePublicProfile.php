<?php
$pageTitle = (string) ($viewData['pageTitle'] ?? 'Production Profile - FilmGig');
$profileImage = (string) ($viewData['profileImage'] ?? '');
$contactName = (string) ($viewData['contactName'] ?? '');
$companyName = (string) ($viewData['companyName'] ?? '');
$email = (string) ($viewData['email'] ?? '');
$address = (string) ($viewData['address'] ?? '');
$website = (string) ($viewData['website'] ?? '');
$bio = (string) ($viewData['bio'] ?? '');
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm"
                style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;">Production Profile</span>
                <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($companyName) ?></h1>
                <p class="mb-0">Learn more about this production company.</p>
            </section>

            <section class="row g-4">
                <aside class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Company Avatar</h2>
                            <div class="rounded-circle overflow-hidden mx-auto mb-3 border border-3"
                                style="width: 150px; height: 150px; border-color: #E5E7EB !important;">
                                <img src="<?= htmlspecialchars($profileImage) ?>"
                                    alt="Production Profile Picture"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <p class="mb-1"><strong>Contact:</strong> <?= htmlspecialchars($contactName) ?></p>
                            <p class="mb-0"><strong>Company:</strong> <?= htmlspecialchars($companyName) ?></p>
                        </div>
                    </div>
                </aside>

                <section class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">Contact Information</h2>
                            <p class="mb-2"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></p>
                            <?php if ($address !== ''): ?>
                                <p class="mb-2"><strong>Address:</strong> <?= htmlspecialchars($address) ?></p>
                            <?php endif; ?>
                            <?php if ($website !== ''): ?>
                                <p class="mb-0"><strong>Website:</strong> <a href="<?= htmlspecialchars($website) ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($website) ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3" style="color: #172554;">About the Company</h2>
                            <p class="mb-0" style="color: #1F2937;"><?= nl2br(htmlspecialchars($bio !== '' ? $bio : 'No company bio has been provided yet.')) ?></p>
                        </div>
                    </div>
                </section>
            </section>

            <div class="mt-4">
                <a href="/gigs" class="btn btn-outline-dark">Back to Gigs</a>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>

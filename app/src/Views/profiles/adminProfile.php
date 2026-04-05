<?php
$pageTitle = $viewModel->pageTitle;
$form = $viewModel->form;
$errors = $viewModel->errors;
$successMessage = $viewModel->successMessage;
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

            <?php if ($successMessage !== null): ?>
                <div class="alert alert-success shadow-sm border-0 rounded-4 mb-4"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>

            <form method="POST" action="/profile" enctype="multipart/form-data">
                <section class="row g-4">
                    <aside class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4 text-center">
                                <h2 class="h5 fw-bold mb-3" style="color: #172554;">Profile Picture</h2>
                                <div class="rounded-circle overflow-hidden mx-auto mb-3 border border-3"
                                    style="width: 150px; height: 150px; border-color: #E5E7EB !important;">
                                    <img src="<?= htmlspecialchars($viewModel->profileImage) ?>"
                                        alt="Current Profile Picture"
                                        class="w-100 h-100 object-fit-cover">
                                </div>
                                <input type="file" class="form-control<?= isset($errors['profileImage']) ? ' is-invalid' : '' ?>" name="profileImage" accept="image/*">
                                <?php if (isset($errors['profileImage'])): ?>
                                    <div class="invalid-feedback d-block text-start"><?= htmlspecialchars($errors['profileImage']) ?></div>
                                <?php endif; ?>
                                <small class="d-block mt-2" style="color: #1F2937;">PNG, JPG, GIF, or WEBP, max 5MB.</small>
                            </div>
                        </div>
                    </aside>

                    <section class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold mb-3" style="color: #172554;">Company Information</h2>

                                <div class="mb-3">
                                    <label for="profileUsername" class="form-label fw-semibold">Username</label>
                                    <input type="text" id="profileUsername" name="username" class="form-control form-control-lg<?= isset($errors['username']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($form['username']) ?>" pattern="[A-Za-z0-9.]+" title="Only letters, numbers, and dots (.) are allowed">
                                    <?php if (isset($errors['username'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['username']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="profileCompanyName" class="form-label fw-semibold">Company Name</label>
                                    <input type="text" id="profileCompanyName" name="companyName" class="form-control form-control-lg<?= isset($errors['companyName']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($form['companyName']) ?>">
                                    <?php if (isset($errors['companyName'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['companyName']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="profileContactName" class="form-label fw-semibold">Contact Person Name</label>
                                    <input type="text" id="profileContactName" name="contactName" class="form-control form-control-lg<?= isset($errors['contactName']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($form['contactName']) ?>">
                                    <?php if (isset($errors['contactName'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['contactName']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="profileEmail" class="form-label fw-semibold">Email</label>
                                    <input type="email" id="profileEmail" name="email" class="form-control form-control-lg<?= isset($errors['email']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($form['email']) ?>">
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="profileAddress" class="form-label fw-semibold">Address</label>
                                    <input type="text" id="profileAddress" name="address" class="form-control form-control-lg<?= isset($errors['address']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($form['address']) ?>">
                                    <?php if (isset($errors['address'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['address']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="profileWebsite" class="form-label fw-semibold">Website</label>
                                    <input type="url" id="profileWebsite" name="website" class="form-control form-control-lg<?= isset($errors['website']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($form['website']) ?>">
                                    <?php if (isset($errors['website'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['website']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold mb-3" style="color: #172554;">About the Company</h2>
                                <label for="profileBio" class="form-label fw-semibold">Bio</label>
                                <textarea id="profileBio" name="bio" rows="5" class="form-control<?= isset($errors['bio']) ? ' is-invalid' : '' ?>"><?= htmlspecialchars($form['bio']) ?></textarea>
                                <?php if (isset($errors['bio'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['bio']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                </section>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-lg text-white" style="background-color: #B91C1C; border-color: #B91C1C;">Save Changes</button>
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
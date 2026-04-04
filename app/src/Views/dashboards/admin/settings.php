<?php
$pageTitle = $viewModel->pageTitle;
$settings = $viewModel->form;
?>

<div class="d-flex flex-column min-vh-100" style="background-color: #E5E7EB;">
    <?php include __DIR__ . '/../../partials/header.php'; ?>

    <main class="flex-grow-1 py-5">
        <div class="container">
            <section class="rounded-4 p-4 p-md-5 mb-4 text-white shadow-sm"
                style="background: linear-gradient(120deg, #172554 0%, #1F2937 100%);">
                <span class="badge mb-3" style="background-color: #FBBF24; color: #172554;"><?= htmlspecialchars($viewModel->badgeLabel) ?></span>
                <h1 class="display-6 fw-bold mb-2"><?= htmlspecialchars($viewModel->heroTitle) ?></h1>
                <p class="mb-0"><?= htmlspecialchars($viewModel->heroDescription) ?></p>
            </section>

            <form>
                <div class="row g-4">
                    <aside class="col-12 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4 text-center">
                                <h2 class="h5 fw-bold mb-3" style="color: #172554;">Profile Picture</h2>
                                <div class="rounded-circle overflow-hidden mx-auto mb-3 border border-3"
                                    style="width: 150px; height: 150px; border-color: #E5E7EB !important;">
                                    <img src="<?= htmlspecialchars($viewModel->profileImage) ?>"
                                        alt="Current Profile Picture" class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="d-grid gap-2">
                                    <input type="file" class="d-none" id="adminProfilePicture" accept="image/*">
                                    <label for="adminProfilePicture" class="btn btn-outline-secondary fw-semibold">Choose New Photo</label>
                                    <small style="color: #1F2937;">PNG or JPG, max 5MB.</small>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <section class="col-12 col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold mb-3" style="color: #172554;">Company Information</h2>

                                <div class="mb-3">
                                    <label for="adminUsername" class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control form-control-lg" id="adminUsername" value="<?= htmlspecialchars($settings['username']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="adminCompanyName" class="form-label fw-semibold">Company Name</label>
                                    <input type="text" class="form-control form-control-lg" id="adminCompanyName" value="<?= htmlspecialchars($settings['companyName']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="adminContactName" class="form-label fw-semibold">Contact Person Name</label>
                                    <input type="text" class="form-control form-control-lg" id="adminContactName" value="<?= htmlspecialchars($settings['contactName']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="adminEmail" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" class="form-control form-control-lg" id="adminEmail" value="<?= htmlspecialchars($settings['email']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="adminAddress" class="form-label fw-semibold">Address</label>
                                    <input type="text" class="form-control form-control-lg" id="adminAddress" value="<?= htmlspecialchars($settings['address']) ?>">
                                </div>

                                <div>
                                    <label for="adminFoundedDate" class="form-label fw-semibold">Founded Date</label>
                                    <input type="date" class="form-control form-control-lg" id="adminFoundedDate" value="<?= htmlspecialchars($settings['foundedDate']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold mb-3" style="color: #172554;">Organization Bio</h2>
                                <label for="adminBio" class="form-label fw-semibold">Update Bio</label>
                                <textarea class="form-control" id="adminBio" rows="5"><?= htmlspecialchars($settings['bio']) ?></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-lg text-white" style="background-color: #B91C1C; border-color: #B91C1C;">Save Changes</button>
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../../partials/footer.php'; ?>
</div>
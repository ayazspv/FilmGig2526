<?php $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>
<?php /** @var \App\ViewModels\AuthViewModel $viewModel */ ?>
<div class="d-flex flex-column min-vh-100">
    <?php $pageTitle = $viewModel->pageTitle; ?>
    <?php include __DIR__ . '/../partials/header.php'; ?>
    <section class="py-5 flex-grow-1 d-flex align-items-center" style="background-color: #E5E7EB;">
        <div class="container">
            <div class="row g-4 align-items-stretch justify-content-center">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="h-100 rounded-4 shadow-sm overflow-hidden text-white d-flex flex-column justify-content-end p-4"
                        style="background: linear-gradient(rgba(23, 37, 84, 0.5), rgba(23, 37, 84, 0.8)), url('https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;">
                        <span class="badge mb-3 align-self-start" style="background-color: #FBBF24; color: #172554;">FilmGig</span>
                        <h2 class="fw-bold mb-2">Create Your Account</h2>
                        <p class="mb-0">Join creators and productions in one focused platform.</p>
                    </div>
                </div>

                <div class="col-12 col-md-10 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="h3 fw-bold mb-2" style="color: #172554;">Join FilmGig</h1>
                            <p class="mb-4" style="color: #1F2937;">Set up your account and start discovering opportunities.</p>

                            <?php if (!empty($viewModel->errors)) : ?>
                                <div class="alert alert-danger border-0" role="alert">
                                    <p class="fw-semibold mb-2">Please fix the following issues:</p>
                                    <?php if (isset($viewModel->errors['general'])) : ?>
                                        <p class="mb-2"><?= $escape((string) $viewModel->errors['general']) ?></p>
                                    <?php endif; ?>
                                    <ul class="mb-0 ps-3">
                                        <?php foreach ($viewModel->errors as $field => $error) : ?>
                                            <?php if ($field === 'general') { continue; } ?>
                                            <li><?= $escape((string) $error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="/signup" method="POST" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="username" class="form-label fw-semibold">Username</label>
                                        <input type="text" class="form-control form-control-lg" id="username" name="username" value="<?= $escape((string) ($viewModel->oldInput['username'] ?? '')) ?>" pattern="[A-Za-z0-9.]+" title="Only letters, numbers, and dots (.) are allowed" required>
                                        <small class="text-muted">Only letters, numbers, and dots (.).</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="role" class="form-label fw-semibold">Account Type</label>
                                        <select class="form-select form-select-lg" id="role" name="role" data-signup-role-select required>
                                            <option value="">Choose a role</option>
                                            <?php foreach ($viewModel->roleOptions as $roleOption) : ?>
                                                <option value="<?= $escape((string) $roleOption['value']) ?>" <?= (($viewModel->oldInput['role'] ?? '') === $roleOption['value']) ? 'selected' : '' ?>>
                                                    <?= $escape((string) $roleOption['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mt-0">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold mt-3">Full Name</label>
                                        <input type="text" class="form-control form-control-lg" id="name" name="name" value="<?= $escape((string) ($viewModel->oldInput['name'] ?? '')) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold mt-3">Email address</label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?= $escape((string) ($viewModel->oldInput['email'] ?? '')) ?>" required>
                                    </div>
                                </div>

                                <div class="row g-3 mt-0">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label fw-semibold mt-3">Password</label>
                                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kvkNr" class="form-label fw-semibold mt-3">Chamber of Commerce Number</label>
                                        <input type="text" class="form-control form-control-lg" id="kvkNr" name="kvkNr" maxlength="8" inputmode="numeric" value="<?= $escape((string) ($viewModel->oldInput['kvkNr'] ?? '')) ?>" required>
                                    </div>
                                </div>

                                <div class="row g-3 mt-0">
                                    <div class="col-12">
                                        <label for="address" class="form-label fw-semibold mt-3">Address</label>
                                        <input type="text" class="form-control form-control-lg" id="address" name="address" value="<?= $escape((string) ($viewModel->oldInput['address'] ?? '')) ?>" placeholder="Optional">
                                    </div>
                                    <div class="col-12">
                                        <label for="bio" class="form-label fw-semibold mt-3">Bio</label>
                                        <textarea class="form-control form-control-lg" id="bio" name="bio" rows="3" placeholder="Tell us a little about yourself or your company"><?= $escape((string) ($viewModel->oldInput['bio'] ?? '')) ?></textarea>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-4 border bg-white" data-signup-role-section="productionHouse">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h2 class="h5 fw-bold mb-0" style="color: #172554;">Production Company Details</h2>
                                        <span class="badge text-bg-warning">Required for companies</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="companyName" class="form-label fw-semibold mt-2">Company Name</label>
                                            <input type="text" class="form-control form-control-lg" id="companyName" name="companyName" data-signup-required value="<?= $escape((string) ($viewModel->oldInput['companyName'] ?? '')) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="website" class="form-label fw-semibold mt-2">Website</label>
                                            <input type="url" class="form-control form-control-lg" id="website" name="website" value="<?= $escape((string) ($viewModel->oldInput['website'] ?? '')) ?>" placeholder="https://example.com">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 rounded-4 border bg-white d-none" data-signup-role-section="freelancer">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h2 class="h5 fw-bold mb-0" style="color: #172554;">Freelancer Details</h2>
                                        <span class="badge text-bg-info">Required for freelancers</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="dateOfBirth" class="form-label fw-semibold mt-2">Date of Birth</label>
                                            <input type="date" class="form-control form-control-lg" id="dateOfBirth" name="dateOfBirth" data-signup-required value="<?= $escape((string) ($viewModel->oldInput['dateOfBirth'] ?? '')) ?>" max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                                            <small class="text-muted">You must be at least 18 years old.</small>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-lg text-white w-100 mt-4" style="background-color: #B91C1C; border-color: #B91C1C;">Join Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>

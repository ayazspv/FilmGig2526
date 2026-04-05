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
                        style="background: linear-gradient(rgba(23, 37, 84, 0.5), rgba(23, 37, 84, 0.8)), url('https://images.unsplash.com/photo-1574717024453-3540562e8b2d?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;">
                        <span class="badge mb-3 align-self-start" style="background-color: #FBBF24; color: #172554;">Security</span>
                        <h2 class="fw-bold mb-2">Set a New Password</h2>
                        <p class="mb-0">Choose a strong password to protect your FilmGig account.</p>
                    </div>
                </div>

                <div class="col-12 col-md-10 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="h3 fw-bold mb-2" style="color: #172554;">Reset Password</h1>
                            <p class="mb-4" style="color: #1F2937;">Create a new password for your verified account.</p>

                            <?php if (!empty($viewModel->errors)) : ?>
                                <div class="alert alert-danger border-0" role="alert">
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

                            <?php if (!empty($viewModel->oldInput['username'])) : ?>
                                <div class="alert alert-info border-0" role="alert">
                                    Resetting password for user: <strong><?= $escape((string) $viewModel->oldInput['username']) ?></strong>
                                </div>
                            <?php endif; ?>

                            <form action="/reset-password" method="POST">
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">New Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                </div>
                                <div class="mb-4">
                                    <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
                                    <input type="password" class="form-control form-control-lg" id="confirmPassword" name="confirmPassword" required>
                                </div>
                                <button type="submit" class="btn btn-lg text-white w-100" style="background-color: #B91C1C; border-color: #B91C1C;">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>

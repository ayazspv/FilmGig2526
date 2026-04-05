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
                        style="background: linear-gradient(rgba(23, 37, 84, 0.5), rgba(23, 37, 84, 0.8)), url('https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;">
                        <span class="badge mb-3 align-self-start" style="background-color: #FBBF24; color: #172554;">Account Support</span>
                        <h2 class="fw-bold mb-2">Forgot Password?</h2>
                        <p class="mb-0">Verify your identity with username, email, and KVK number to continue.</p>
                    </div>
                </div>

                <div class="col-12 col-md-10 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="h3 fw-bold mb-2" style="color: #172554;">Reset Request</h1>
                            <p class="mb-4" style="color: #1F2937;">Enter your username, signup email, and Chamber of Commerce number.</p>

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

                            <form action="/forget-password" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control form-control-lg" id="username" name="username" value="<?= $escape((string) ($viewModel->oldInput['username'] ?? '')) ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?= $escape((string) ($viewModel->oldInput['email'] ?? '')) ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label for="kvkNr" class="form-label fw-semibold">Chamber of Commerce Number</label>
                                    <input type="text" class="form-control form-control-lg" id="kvkNr" name="kvkNr" maxlength="8" inputmode="numeric" value="<?= $escape((string) ($viewModel->oldInput['kvkNr'] ?? '')) ?>" required>
                                </div>
                                <button type="submit" class="btn btn-lg text-white w-100" style="background-color: #172554; border-color: #172554;">Continue</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>

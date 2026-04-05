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
                        style="background: linear-gradient(rgba(23, 37, 84, 0.5), rgba(23, 37, 84, 0.8)), url('https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;">
                        <span class="badge mb-3 align-self-start" style="background-color: #FBBF24; color: #172554;">FilmGig</span>
                        <h2 class="fw-bold mb-2">Welcome Back</h2>
                        <p class="mb-0">Sign in to continue exploring gigs and growing your film career.</p>
                    </div>
                </div>

                <div class="col-12 col-md-10 col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4 p-md-5">
                            <h1 class="h3 fw-bold mb-2" style="color: #172554;">Sign In</h1>
                            <p class="mb-4" style="color: #1F2937;">Access your profile and apply to new gigs.</p>

                            <?php if (!empty($viewModel->successMessage)) : ?>
                                <div class="alert alert-success border-0" role="alert">
                                    <?= $escape((string) $viewModel->successMessage) ?>
                                </div>
                            <?php endif; ?>

                            <form action="/signin" method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                </div>
                                <div class="mb-4">
                                    <a href="/forget-password" class="text-decoration-none fw-semibold" style="color: #172554;">Forgot Password?</a>
                                </div>
                                <button type="submit" class="btn btn-lg text-white w-100" style="background-color: #172554; border-color: #172554;">Sign In</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>
<div class="d-flex flex-column min-vh-100">
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
                            <p class="mb-4" style="color: #1F2937;">Confirm your email and create a new password.</p>

                            <form action="/ChangePassword" method="POST">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Type your email again</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                </div>
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

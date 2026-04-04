<div class="d-flex flex-column min-vh-100">
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

                            <form action="#" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Full Name</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email address</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                </div>
                                <div class="mb-4">
                                    <label for="kvkNr" class="form-label fw-semibold">Chamber of Commerce Number</label>
                                    <input type="text" class="form-control form-control-lg" id="kvkNr" name="kvkNr">
                                </div>
                                <button type="button" class="btn btn-lg text-white w-100" style="background-color: #B91C1C; border-color: #B91C1C;" name="createUser" id="createUser">Join Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="js/addUser.js"></script>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</div>

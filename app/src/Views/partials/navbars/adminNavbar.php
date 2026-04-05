<nav class="navbar navbar-expand-lg navbar-dark py-3" style="background-color: #172554;">
    <?php $currentRole = (string) ($_SESSION['auth_user_role'] ?? ''); ?>
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/">
            <span class="badge rounded-pill" style="background-color: #FBBF24; color: #172554;">FG</span>
            <span>FilmGig</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAdmin"
            aria-controls="navbarNavAdmin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAdmin">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">Dashboard</a>
                </li>
                <?php if ($currentRole === 'productionHouse'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/gigs">My Gigs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/submissions/received">Submissions</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard/gigs/new">Add Gigs</a>
                </li>
            </ul>
            <div class="dropdown mt-3 mt-lg-0">
                <a href="#" class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2"
                    id="profileDropdownAdmin" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= htmlspecialchars((string) ($navbarProfileImage ?? 'https://images.unsplash.com/photo-1568602471122-7832951cc4c5?auto=format&fit=crop&w=100&q=80')) ?>" alt="Profile Picture" class="rounded-circle object-fit-cover flex-shrink-0" width="28" height="28" style="object-fit: cover;">
                    <span><?php echo isset($_SESSION['auth_user_name']) ? htmlspecialchars($_SESSION['auth_user_name']) : 'User'; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownAdmin">
                    <li><a class="dropdown-item" href="/profile">My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/signout">Log Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
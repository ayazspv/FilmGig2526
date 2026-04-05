<nav class="navbar navbar-expand-lg navbar-dark py-3" style="background-color: #172554;">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/">
            <span class="badge rounded-pill" style="background-color: #FBBF24; color: #172554;">FG</span>
            <span>FilmGig</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavFreelance"
            aria-controls="navbarNavFreelance" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavFreelance">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link" href="/gigs">Find Gigs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard/submissions">My Submissions</a>
                </li>
            </ul>
            <div class="dropdown mt-3 mt-lg-0">
                <a href="#" class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2"
                    id="profileDropdownFreelance" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&q=80" alt="Profile Picture" class="rounded-circle" width="28" height="28">
                    <span><?php echo isset($_SESSION['auth_user_name']) ? htmlspecialchars($_SESSION['auth_user_name']) : 'User'; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdownFreelance">
                    <li><a class="dropdown-item" href="/profile">My Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/signout">Log Out</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>
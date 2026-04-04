<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">FilmGig</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/gigs">Gigs List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/post-gig">Add Gigs</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <!-- Profile Section -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle"
                        id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="/path/to/profile-pic.jpg" alt="Profile Picture" class="rounded-circle header-pfp"
                            width="40" height="40">
                        <span class="ms-2">
                            <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="/profile">My Profile</a></li>
                        <li><a class="dropdown-item" href="/settings">Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="/SignoutAction">Log Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
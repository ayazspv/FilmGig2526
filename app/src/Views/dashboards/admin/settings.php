<?php include __DIR__ . '/../../partials/header.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <!-- Settings Header -->
    <header class="text-center mb-4">
        <h1 class="display-5">Admin Settings</h1>
        <p class="text-muted">Manage your profile and preferences</p>
    </header>

    <!-- Settings Form -->
    <form>
        <div class="row">
            <!-- Left Side: Profile Picture -->
            <aside class="col-md-4 text-center mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Profile Picture</h5>
                        <div class="current-pfp mb-3 settings-pfp-container">
                            <img src="[Current Profile Picture URL]" alt="Current Profile Picture"
                                class="img-fluid rounded-circle settings-pfp" width="150" height="150">
                        </div>
                        <div class="change-pfp">
                            <label for="profilePicture" class="form-label">Change Profile Picture</label>
                            <input type="file" class="form-control" id="profilePicture">
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Right Side: User Information -->
            <section class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Personal Information</h5>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="[Current Username]">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="name" value="[Current Company Name]">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Contact Person Name</label>
                            <input type="text" class="form-control" id="name" value="[Current COntact Person cName]">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" value="[Current Email]">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" value="[Current Address]">
                        </div>
                        <div class="mb-3">
                            <label for="dateOfBirth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dateOfBirth" value="[Current Date of Birth]">
                        </div>
                    </div>
                </div>

                <!-- Bio Section -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Bio</h5>
                        <div class="change-bioBox">
                            <label for="bio" class="form-label">Update Bio</label>
                            <textarea class="form-control" id="bio" rows="4">[Current Bio]</textarea>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Save Changes Button -->
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../../partials/footer.php'; ?>
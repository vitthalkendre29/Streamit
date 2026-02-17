<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <a href="index.php">StreamIt</a>
        </div>
        <?php if (isLoggedIn()): ?>
        <div class="logo">
            <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <?php endif; ?>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="search.php">Browse</a>
            
            <?php if (isLoggedIn()): ?>
                <a href="upload.php">Upload</a>
                <a href="my_profile.php">My Profile</a>
                <a href="my_videos.php">My Videos</a>
                <a href="subscription.php">Subscription</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            <?php endif; ?>
            
            <?php if (isAdmin()): ?>
                <a href="admin/" class="btn btn-secondary">Admin</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
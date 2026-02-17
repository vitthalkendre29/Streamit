<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

// Fetch statistics
$totalVideos = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
$pendingVideos = $pdo->query("SELECT COUNT(*) FROM videos WHERE status = 'pending'")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalViews = $pdo->query("SELECT SUM(views) FROM videos")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/stylwadmin.css">
</head>
<body>
    <div class="container">
        <h1>🎬 Admin Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?= $totalVideos ?></span>
                <span class="stat-label">Total Videos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $pendingVideos ?></span>
                <span class="stat-label">Pending Videos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $totalUsers ?></span>
                <span class="stat-label">Registered Users</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= $totalViews ?: 0 ?></span>
                <span class="stat-label">Total Views</span>
            </div>
        </div>

        <div class="nav-links">
            <a href="videos.php" class="btn">📹 Manage Videos</a>
            <a href="categories.php" class="btn">📂 Categories</a>
            <a href="users.php" class="btn">👥 Users</a>
            <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>

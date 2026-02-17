<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$featuredVideos = getVideos('approved', null, null, 6);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StreamIt - Video Streaming Platform</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <div class="hero">
        <div class="container">
            <h1>Welcome to StreamIt</h1>
            <p>Your personal video streaming platform with complete control and privacy</p>
            
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search for videos..." required>
                <button type="submit" style="color : red">🔍</button>
            </form>
            
            <div style="margin-top: 2rem;">
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                <?php else: ?>
                    <a href="upload.php" class="btn btn-primary">Upload Video</a>
                <?php endif; ?>
                <a href="search.php" class="btn btn-secondary">Browse Videos</a>
            </div>
        </div>
    </div>
    
    <!-- Featured Videos -->
    <div class="container">
        <h2 style="color: white; margin-bottom: 2rem;">Featured Videos</h2>
        <div class="video-grid">
            <?php if (empty($featuredVideos)): ?>
                <div class="video-card demo-card">
                    <div class="video-thumbnail">▶️</div>
                    <div class="video-info">
                        <div class="video-title">Getting Started with StreamIt</div>
                        <div class="video-meta">Admin • 2 days ago • Tutorial</div>
                    </div>
                </div>
                <div class="video-card demo-card">
                    <div class="video-thumbnail">▶️</div>
                    <div class="video-info">
                        <div class="video-title">Platform Features Overview</div>
                        <div class="video-meta">Admin • 1 week ago • Demo</div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($featuredVideos as $video): ?>
                    <div class="video-card" onclick="location.href='video.php?id=<?php echo $video['id']; ?>'">
                        <div class="video-thumbnail">
                            <?php if ($video['thumbnail']): ?>
                                <img src="uploads/thumbnails/<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="Thumbnail">
                            <?php else: ?>
                                ▶️
                            <?php endif; ?>
                        </div>
                        <div class="video-info">
                            <div class="video-title"><?php echo htmlspecialchars($video['title']); ?></div>
                            <div class="video-meta">
                                <?php echo htmlspecialchars($video['username']); ?> • 
                                <?php echo timeAgo($video['upload_date']); ?> • 
                                <?php echo htmlspecialchars($video['category_name']); ?> • 
                                <?php echo number_format($video['views']); ?> views
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 StreamIt. A secure, private video streaming platform.</p>
    </div>

    <script src="./js/main.js"></script>
</body>
</html>
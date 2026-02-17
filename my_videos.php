<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$videos = getUserVideos($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Videos - StreamIt</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>My Uploaded Videos</h1>
        <?php if (empty($videos)): ?>
            <p>You haven't uploaded any videos yet.</p>
        <?php else: ?>
            <div class="video-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-card" onclick="location.href='video.php?id=<?php echo $video['id']; ?>'">
                        <div class="video-thumbnail">
                            <?php if ($video['thumbnail']): ?>
                                <img src="uploads/thumbnails/<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="Thumbnail">
                            <?php else: ?>▶️<?php endif; ?>
                        </div>
                        <div class="video-info">
                            <div class="video-title"><?php echo htmlspecialchars($video['title']); ?></div>
                            <div class="video-meta">
                                <?php echo timeAgo($video['upload_date']); ?> • 
                                <?php echo htmlspecialchars($video['category_name']); ?> • 
                                <?php echo number_format($video['views']); ?> views
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="./js/main.js"></script>
</body>
</html>

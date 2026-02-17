<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/subscription_functions.php';

$videoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$videoId) {
    header('Location: index.php');
    exit;
}

// Get video details
$stmt = $pdo->prepare("
    SELECT v.*, u.username, u.has_subscription as uploader_has_subscription, c.name as category_name 
    FROM videos v 
    JOIN users u ON v.user_id = u.id 
    JOIN categories c ON v.category_id = c.id 
    WHERE v.id = ? AND v.status = 'approved'
");
$stmt->execute([$videoId]);
$video = $stmt->fetch();

if (!$video) {
    header('Location: index.php');
    exit;
}

// Check if user can view this video
$userId = isLoggedIn() ? $_SESSION['user_id'] : null;
$canView = canViewVideo($videoId, $userId);

if (!$canView) {
    // Show access denied page
    $requiresSubscription = true;
} else {
    $requiresSubscription = false;
    
    // Increment view count only if user can view
    $stmt = $pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
    $stmt->execute([$videoId]);
}

// Get like info
$likeCount = getVideoLikeCount($videoId);
$userLiked = isLoggedIn() ? hasUserLikedVideo($_SESSION['user_id'], $videoId) : false;

// Get comment info
$commentCount = getVideoCommentCount($videoId);
$comments = $canView ? getVideoComments($videoId, 10, 0) : [];

// Get related videos (filtered by subscription)
$relatedVideos = getVideos('approved', $video['category_id'], null, 6);
$relatedVideos = array_filter($relatedVideos, function($v) use ($videoId) {
    return $v['id'] != $videoId;
});
$relatedVideos = array_slice($relatedVideos, 0, 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?> - StreamIt</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* Like/Comment Styles */
        .video-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #2d3436;
            border: 2px solid #444;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        .action-btn:hover {
            background: #444;
            border-color: #667eea;
        }
        .action-btn.liked {
            background: #667eea;
            border-color: #667eea;
        }
        .action-btn .icon {
            font-size: 20px;
        }
        .action-btn .count {
            font-weight: bold;
        }
        
        /* Access Denied Styles */
        .access-denied {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            color: white;
            margin: 2rem 0;
        }
        
        .access-denied-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .access-denied h2 {
            margin: 0 0 1rem 0;
            font-size: 2rem;
        }
        
        .access-denied p {
            font-size: 1.1rem;
            margin: 1rem 0;
            opacity: 0.9;
        }
        
        .subscriber-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 1rem;
        }
        
        /* Comment Section */
        .comments-section {
            margin-top: 40px;
            padding: 30px;
            background: #2d3436;
            border-radius: 10px;
        }
        .comments-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: white;
        }
        .comment-form {
            margin-bottom: 30px;
        }
        .comment-input {
            width: 100%;
            padding: 15px;
            background: #1e272e;
            border: 2px solid #444;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 10px;
        }
        .comment-input:focus {
            outline: none;
            border-color: #667eea;
        }
        .comment-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        /* Comments List */
        .comments-list {
            margin-top: 20px;
        }
        .comment {
            background: #1e272e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .comment-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .comment-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }
        .comment-username {
            font-weight: bold;
            color: white;
        }
        .comment-time {
            color: #888;
            font-size: 12px;
        }
        .comment-text {
            color: #ccc;
            line-height: 1.6;
            margin: 10px 0;
        }
        .comment-btns {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        .comment-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 13px;
            transition: color 0.3s;
        }
        .comment-btn:hover {
            color: #667eea;
        }
        .delete-btn {
            color: #e74c3c;
        }
        .delete-btn:hover {
            color: #c0392b;
        }
        
        /* Replies */
        .replies {
            margin-left: 50px;
            margin-top: 15px;
        }
        .reply {
            background: #232c35;
            border-left: 3px solid #667eea;
        }
        
        /* Loading/Empty States */
        .loading {
            text-align: center;
            padding: 20px;
            color: #888;
        }
        .no-comments {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        
        /* Notification */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: #28a745;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            animation: slideIn 0.3s;
        }
        .notification.error {
            background: #dc3545;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="video-page">
            <?php if ($requiresSubscription): ?>
                <!-- Access Denied Message -->
                <div class="access-denied">
                    <div class="access-denied-icon">🔒</div>
                    <h2>Subscriber-Only Content</h2>
                    <p>This video is exclusive to subscribers. Upgrade your account to access this and other premium content!</p>
                    <div class="subscriber-badge">SUBSCRIBER EXCLUSIVE</div>
                    <div style="margin-top: 2rem;">
                        <?php if (!isLoggedIn()): ?>
                            <a href="login.php?redirect=video.php?id=<?php echo $videoId; ?>" class="btn btn-primary" style="margin-right: 1rem;">Login</a>
                        <?php endif; ?>
                        <a href="subscription.php" class="btn btn-secondary">View Subscription Plans</a>
                    </div>
                </div>
                
                <!-- Video Info (limited) -->
                <div class="video-details">
                    <h1 class="video-title"><?php echo htmlspecialchars($video['title']); ?></h1>
                    <div class="video-stats">
                        <span class="stat">👁️ <?php echo number_format($video['views']); ?> views</span>
                        <span class="stat">📅 <?php echo timeAgo($video['upload_date']); ?></span>
                        <span class="stat" style="color: #667eea;">🔒 Subscriber Only</span>
                    </div>
                    
                    <div class="video-uploader">
                        <div class="uploader-avatar">
                            <?php echo strtoupper(substr($video['username'], 0, 1)); ?>
                        </div>
                        <div class="uploader-details">
                            <h3><?php echo htmlspecialchars($video['username']); ?></h3>
                            <p class="category">Category: <?php echo htmlspecialchars($video['category_name']); ?></p>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Full Video Player -->
                <div class="video-player">
                    <video controls autoplay style="width: 100%; max-height: 600px; background: #000;">
                        <source src="uploads/videos/<?php echo htmlspecialchars($video['filename']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                
                <!-- Video Details -->
                <div class="video-details">
                    <h1 class="video-title">
                        <?php echo htmlspecialchars($video['title']); ?>
                        <?php if ($video['is_subscriber_only']): ?>
                            <span class="subscriber-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.7rem; margin-left: 0.5rem;">SUBSCRIBER</span>
                        <?php endif; ?>
                    </h1>
                    
                    <div class="video-stats">
                        <span class="stat">👁️ <?php echo number_format($video['views']); ?> views</span>
                        <span class="stat">📅 <?php echo timeAgo($video['upload_date']); ?></span>
                    </div>
                    
                    <div class="video-actions">
                        <button class="action-btn <?php echo $userLiked ? 'liked' : ''; ?>" id="like-btn" onclick="toggleLike()">
                            <span class="icon">👍</span>
                            <span class="count" id="like-count"><?php echo number_format($likeCount); ?></span>
                        </button>
                        <button class="action-btn" onclick="shareVideo()">
                            <span class="icon">🔗</span>
                            <span>Share</span>
                        </button>
                    </div>
                    
                    <div class="video-uploader">
                        <div class="uploader-avatar">
                            <?php echo strtoupper(substr($video['username'], 0, 1)); ?>
                        </div>
                        <div class="uploader-details">
                            <h3><?php echo htmlspecialchars($video['username']); ?></h3>
                            <p class="category">Category: <?php echo htmlspecialchars($video['category_name']); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($video['description']): ?>
                        <div class="video-description">
                            <h4>Description</h4>
                            <p><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            
                <!-- Comments Section -->
                <div class="comments-section">
                    <div class="comments-header">
                        <span id="comment-count"><?php echo number_format($commentCount); ?></span> Comments
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <div class="comment-form">
                            <textarea 
                                id="comment-input" 
                                class="comment-input" 
                                placeholder="Add a comment..."
                                maxlength="1000"
                            ></textarea>
                            <div class="comment-actions">
                                <button class="btn btn-secondary" onclick="clearComment()">Cancel</button>
                                <button class="btn btn-primary" onclick="postComment()">Comment</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="no-comments comments-header">
                            <p><a class="comments-header" href="login.php">Login</a> to add a comment</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="comments-list" id="comments-list">
                        <?php if (empty($comments)): ?>
                            <div class="no-comments">No comments yet. Be the first to comment!</div>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <?php include 'includes/comment_template.php'; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($relatedVideos)): ?>
                <div class="related-videos">
                    <h3>Related Videos</h3>
                    <div class="related-videos-grid">
                        <?php foreach ($relatedVideos as $relatedVideo): ?>
                            <div class="related-video-card" onclick="location.href='video.php?id=<?php echo $relatedVideo['id']; ?>'">
                                <div class="related-thumbnail">
                                    <?php if ($relatedVideo['thumbnail']): ?>
                                        <img src="uploads/thumbnails/<?php echo htmlspecialchars($relatedVideo['thumbnail']); ?>" 
                                             alt="<?php echo htmlspecialchars($relatedVideo['title']); ?>">
                                    <?php else: ?>
                                        <div class="default-thumbnail">▶️</div>
                                    <?php endif; ?>
                                    <?php if ($relatedVideo['is_subscriber_only']): ?>
                                        <div style="position: absolute; top: 10px; right: 10px; background: rgba(102, 126, 234, 0.9); color: white; padding: 0.25rem 0.5rem; border-radius: 10px; font-size: 0.7rem;">🔒 SUB</div>
                                    <?php endif; ?>
                                </div>
                                <div class="related-info">
                                    <div class="related-title"><?php echo htmlspecialchars($relatedVideo['title']); ?></div>
                                    <div class="related-meta">
                                        <?php echo htmlspecialchars($relatedVideo['username']); ?> • 
                                        <?php echo number_format($relatedVideo['views']); ?> views
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 StreamIt. A secure, private video streaming platform.</p>
    </div>

    <script src="./js/main.js"></script>
    <script>
        const videoId = <?php echo $videoId; ?>;
        const userId = <?php echo isLoggedIn() ? $_SESSION['user_id'] : 'null'; ?>;
        const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        const canView = <?php echo $canView ? 'true' : 'false'; ?>;
        
        // Toggle Like
        function toggleLike() {
            if (!isLoggedIn) {
                window.location.href = 'login.php';
                return;
            }
            
            if (!canView) {
                showNotification('You need a subscription to interact with this video', 'error');
                return;
            }
            
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=toggle_like&video_id=' + videoId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('like-count').textContent = data.like_count;
                    const likeBtn = document.getElementById('like-btn');
                    if (data.liked) {
                        likeBtn.classList.add('liked');
                    } else {
                        likeBtn.classList.remove('liked');
                    }
                    showNotification(data.message);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        // Post Comment
        function postComment(parentId = null) {
            if (!isLoggedIn) {
                window.location.href = 'login.php';
                return;
            }
            
            if (!canView) {
                showNotification('You need a subscription to comment on this video', 'error');
                return;
            }
            
            const inputId = parentId ? `reply-input-${parentId}` : 'comment-input';
            const commentInput = document.getElementById(inputId);
            const comment = commentInput.value.trim();
            
            if (!comment) {
                showNotification('Please enter a comment', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'add_comment');
            formData.append('video_id', videoId);
            formData.append('comment', comment);
            if (parentId) formData.append('parent_id', parentId);
            
            fetch('ajax_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentInput.value = '';
                    document.getElementById('comment-count').textContent = data.comment_count;
                    location.reload(); // Simple reload to show new comment
                    showNotification(data.message);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        // Delete Comment
        function deleteComment(commentId) {
            if (!confirm('Are you sure you want to delete this comment?')) {
                return;
            }
            
            fetch('ajax_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete_comment&comment_id=${commentId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`comment-${commentId}`).remove();
                    document.getElementById('comment-count').textContent = data.comment_count;
                    showNotification(data.message);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        }
        
        // Clear Comment
        function clearComment() {
            document.getElementById('comment-input').value = '';
        }
        
        // Share Video
        function shareVideo() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo addslashes($video['title']); ?>',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                showNotification('Video link copied to clipboard!');
            }
        }
        
        // Show Notification
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>
</html>
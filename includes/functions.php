<?php
require_once __DIR__ . '/email_functions.php';
require_once __DIR__ . '/subscription_functions.php';

// Sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Get user information
function getUserInfo($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

// Get videos with filters and subscription access
function getVideos($status = 'approved', $category = null, $search = null, $limit = null, $offset = 0) {
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    return getVideosWithSubscriptionFilter($status, $category, $search, $limit, $offset, $userId);
}

// Search videos with pagination and subscription filter
function searchVideos($search = '', $category = null, $limit = 12, $offset = 0) {
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    return getVideosWithSubscriptionFilter('approved', $category, $search, $limit, $offset, $userId);
}

// Count search results with subscription filter
function countSearchVideos($search = '', $category = null) {
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    return countVideosWithSubscriptionFilter($search, $category, $userId);
}

// Get categories
function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

// Get pending videos for admin
function getPendingVideos() {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT v.*, u.username, c.name as category_name 
        FROM videos v 
        JOIN users u ON v.user_id = u.id 
        JOIN categories c ON v.category_id = c.id 
        WHERE v.status = 'pending' 
        ORDER BY v.upload_date DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get admin statistics
function getAdminStats() {
    global $pdo;
    
    $stats = [];
    
    // Total videos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM videos WHERE status = 'approved'");
    $stats['total_videos'] = $stmt->fetch()['count'];
    
    // Pending videos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM videos WHERE status = 'pending'");
    $stats['pending_videos'] = $stmt->fetch()['count'];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $stats['total_users'] = $stmt->fetch()['count'];
    
    // Total views
    $stmt = $pdo->query("SELECT SUM(views) as total FROM videos WHERE status = 'approved'");
    $stats['total_views'] = $stmt->fetch()['total'] ?: 0;
    
    // Add subscription stats
    $subscriptionStats = getSubscriptionStats();
    $stats = array_merge($stats, $subscriptionStats);
    
    return $stats;
}

// Approve video
function approveVideo($videoId) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE videos SET status = 'approved' WHERE id = ?");
    return $stmt->execute([$videoId]);
}

// Reject video
function rejectVideo($videoId) {
    global $pdo;
    
    // Get video info to delete file
    $stmt = $pdo->prepare("SELECT filename FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $video = $stmt->fetch();
    
    if ($video) {
        // Delete file
        $filePath = UPLOAD_PATH . 'videos/' . $video['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
        return $stmt->execute([$videoId]);
    }
    
    return false;
}

// Delete video
function deleteVideo($videoId) {
    global $pdo;
    
    // Get video info to delete file
    $stmt = $pdo->prepare("SELECT filename, thumbnail FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $video = $stmt->fetch();
    
    if ($video) {
        // Delete video file
        $videoPath = UPLOAD_PATH . 'videos/' . $video['filename'];
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }
        
        // Delete thumbnail if exists
        if ($video['thumbnail']) {
            $thumbPath = UPLOAD_PATH . 'thumbnails/' . $video['thumbnail'];
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
        return $stmt->execute([$videoId]);
    }
    
    return false;
}

// Format time ago
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Generate thumbnail (placeholder function)
function generateThumbnail($videoPath, $thumbnailPath) {
    // This would require FFmpeg or similar video processing library
    // For now, return false to indicate no thumbnail generated
    return false;
}

// Validate video file
function validateVideoFile($file) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error.';
        return $errors;
    }
    
    if (!in_array($file['type'], ALLOWED_VIDEO_TYPES)) {
        $errors[] = 'Invalid file type. Only MP4, AVI, MOV, WMV, and WebM files are allowed.';
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File size too large. Maximum allowed size is ' . formatFileSize(MAX_FILE_SIZE) . '.';
    }
    
    return $errors;
}

// Get user's uploaded videos
function getUserVideos($userId, $status = null) {
    global $pdo;
    
    $sql = "SELECT v.*, c.name as category_name 
            FROM videos v 
            JOIN categories c ON v.category_id = c.id 
            WHERE v.user_id = ?";
    
    $params = [$userId];
    
    if ($status) {
        $sql .= " AND v.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY v.upload_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Create category
function createCategory($name, $description = '') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    return $stmt->execute([$name, $description]);
}

// Update category
function updateCategory($id, $name, $description = '') {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
    return $stmt->execute([$name, $description, $id]);
}

// Delete category (only if no videos use it)
function deleteCategory($id) {
    global $pdo;
    
    // Check if any videos use this category
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM videos WHERE category_id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        return false; // Cannot delete category with videos
    }
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

function getVideoLikeCount($videoId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT likes FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $result = $stmt->fetch();
    return $result ? $result['likes'] : 0;
}

/**
 * Check if user liked a video
 */
function hasUserLikedVideo($userId, $videoId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM video_reactions WHERE user_id = ? AND video_id = ?");
    $stmt->execute([$userId, $videoId]);
    return $stmt->fetch() !== false;
}

/**
 * Get comment count for a video
 */
function getVideoCommentCount($videoId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE video_id = ? AND is_approved = 1");
    $stmt->execute([$videoId]);
    $result = $stmt->fetch();
    return $result ? $result['count'] : 0;
}

/**
 * Get comments for a video
 */
function getVideoComments($videoId) {
    global $pdo;
    
    // Get top-level comments
    $stmt = $pdo->prepare("
        SELECT c.*, u.username 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.video_id = ? AND c.parent_id IS NULL AND c.is_approved = 1
        ORDER BY c.created_at DESC 
    ");
    $stmt->execute([$videoId]);
    $comments = $stmt->fetchAll();
    
    // Get replies for each comment
    foreach ($comments as &$comment) {
        $stmt = $pdo->prepare("
            SELECT c.*, u.username 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.parent_id = ? AND c.is_approved = 1
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$comment['id']]);
        $comment['replies'] = $stmt->fetchAll();
    }
    
    return $comments;
}
?>
<?php
/**
 * AJAX Handler for Likes and Comments
 * Handles all AJAX requests for video interactions
 */

require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to perform this action']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'toggle_like':
        toggleLike();
        break;
    
    case 'add_comment':
        addComment();
        break;
    
    case 'delete_comment':
        deleteComment();
        break;
    
    case 'get_comments':
        getComments();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

/**
 * Toggle Like/Unlike for a video
 */
function toggleLike() {
    global $pdo;
    
    $videoId = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
    $userId = $_SESSION['user_id'];
    
    if (!$videoId) {
        echo json_encode(['success' => false, 'message' => 'Invalid video ID']);
        return;
    }
    
    try {
        // Check if user already liked this video
        $stmt = $pdo->prepare("SELECT id, reaction FROM video_reactions WHERE user_id = ? AND video_id = ?");
        $stmt->execute([$userId, $videoId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Unlike - remove the reaction
            $stmt = $pdo->prepare("DELETE FROM video_reactions WHERE id = ?");
            $stmt->execute([$existing['id']]);
            
            // Decrement like count in videos table
            $stmt = $pdo->prepare("UPDATE videos SET likes = GREATEST(0, likes - 1) WHERE id = ?");
            $stmt->execute([$videoId]);
            
            $liked = false;
            $message = 'Like removed';
        } else {
            // Like - add new reaction
            $stmt = $pdo->prepare("INSERT INTO video_reactions (user_id, video_id, reaction) VALUES (?, ?, 'like')");
            $stmt->execute([$userId, $videoId]);
            
            // Increment like count in videos table
            $stmt = $pdo->prepare("UPDATE videos SET likes = likes + 1 WHERE id = ?");
            $stmt->execute([$videoId]);
            
            $liked = true;
            $message = 'Video liked';
        }
        
        // Get updated like count
        $stmt = $pdo->prepare("SELECT COUNT(*) as likes FROM video_reactions WHERE video_id = ?");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'liked' => $liked,
            'like_count' => $video['likes'],
            'message' => $message
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * Add a new comment
 */
function addComment() {
    global $pdo;
    
    $videoId = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $parentId = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $userId = $_SESSION['user_id'];
    
    if (!$videoId || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Video ID and comment are required']);
        return;
    }
    
    if (strlen($comment) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Comment too long (max 1000 characters)']);
        return;
    }
    
    try {
        // Insert comment
        $stmt = $pdo->prepare("
            INSERT INTO comments (user_id, video_id, parent_id, comment, is_approved) 
            VALUES (?, ?, ?, ?, 1)
        ");
        $stmt->execute([$userId, $videoId, $parentId, $comment]);
        
        $commentId = $pdo->lastInsertId();
        
        // Get comment with user info
        $stmt = $pdo->prepare("
            SELECT c.*, u.username 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.id = ?
        ");
        $stmt->execute([$commentId]);
        $newComment = $stmt->fetch();
        
        // Get total comment count for this video
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE video_id = ? AND is_approved = 1");
        $stmt->execute([$videoId]);
        $commentCount = $stmt->fetch()['count'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Comment added successfully',
            'comment' => $newComment,
            'comment_count' => $commentCount
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * Delete a comment
 */
function deleteComment() {
    global $pdo;
    
    $commentId = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $userId = $_SESSION['user_id'];
    
    if (!$commentId) {
        echo json_encode(['success' => false, 'message' => 'Invalid comment ID']);
        return;
    }
    
    try {
        // Check if comment belongs to user
        $stmt = $pdo->prepare("SELECT video_id FROM comments WHERE id = ? AND user_id = ?");
        $stmt->execute([$commentId, $userId]);
        $comment = $stmt->fetch();
        
        if (!$comment) {
            echo json_encode(['success' => false, 'message' => 'Comment not found or unauthorized']);
            return;
        }
        
        // Delete comment and all its replies (CASCADE will handle this)
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        
        // Get updated comment count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE video_id = ? AND is_approved = 1");
        $stmt->execute([$comment['video_id']]);
        $commentCount = $stmt->fetch()['count'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Comment deleted successfully',
            'comment_count' => $commentCount
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

/**
 * Get comments for a video
 */
function getComments() {
    global $pdo;
    
    $videoId = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
    
    if (!$videoId) {
        echo json_encode(['success' => false, 'message' => 'Invalid video ID']);
        return;
    }
    
    try {
        // Get comments
        $stmt = $pdo->prepare("
            SELECT c.*, u.username 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.video_id = ? AND c.parent_id IS NULL AND c.is_approved = 1
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$videoId, $limit, $offset]);
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
        
        // Get total count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE video_id = ? AND is_approved = 1");
        $stmt->execute([$videoId]);
        $total = $stmt->fetch()['count'];
        
        echo json_encode([
            'success' => true,
            'comments' => $comments,
            'total' => $total,
            'has_more' => ($offset + $limit) < $total
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
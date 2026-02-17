<?php
/**
 * Comment Template
 * This file is included in video.php to display each comment
 */
?>
<div class="comment" id="comment-<?php echo $comment['id']; ?>">
    <div class="comment-header">
        <div class="comment-user">
            <div class="comment-avatar">
                <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
            </div>
            <div>
                <div class="comment-username"><?php echo htmlspecialchars($comment['username']); ?></div>
                <div class="comment-time"><?php echo timeAgo($comment['created_at']); ?></div>
            </div>
        </div>
        <?php if (isLoggedIn() && $_SESSION['user_id'] == $comment['user_id']): ?>
            <button class="comment-btn delete-btn" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                🗑️ Delete
            </button>
        <?php endif; ?>
    </div>
    
    <div class="comment-text">
        <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
    </div>
    
    <div class="comment-btns">
        <?php if (isLoggedIn()): ?>
            <button class="comment-btn" onclick="toggleReply(<?php echo $comment['id']; ?>)">
                💬 Reply
            </button>
        <?php endif; ?>
    </div>
    
    <!-- Reply Form (Hidden by default) -->
    <div class="reply-form" id="reply-form-<?php echo $comment['id']; ?>" style="display: none; margin-top: 15px;">
        <textarea 
            id="reply-input-<?php echo $comment['id']; ?>" 
            class="comment-input" 
            placeholder="Write a reply..."
            maxlength="1000"
            style="min-height: 80px;"
        ></textarea>
        <div class="comment-actions" style="margin-top: 10px;">
            <button class="btn btn-secondary btn-sm" onclick="toggleReply(<?php echo $comment['id']; ?>)">Cancel</button>
            <button class="btn btn-primary btn-sm" onclick="postComment(<?php echo $comment['id']; ?>)">Reply</button>
        </div>
    </div>
    
    <!-- Replies -->
    <?php if (!empty($comment['replies'])): ?>
        <div class="replies">
            <?php foreach ($comment['replies'] as $reply): ?>
                <div class="comment reply" id="comment-<?php echo $reply['id']; ?>">
                    <div class="comment-header">
                        <div class="comment-user">
                            <div class="comment-avatar">
                                <?php echo strtoupper(substr($reply['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <div class="comment-username"><?php echo htmlspecialchars($reply['username']); ?></div>
                                <div class="comment-time"><?php echo timeAgo($reply['created_at']); ?></div>
                            </div>
                        </div>
                        <?php if (isLoggedIn() && $_SESSION['user_id'] == $reply['user_id']): ?>
                            <button class="comment-btn delete-btn" onclick="deleteComment(<?php echo $reply['id']; ?>)">
                                🗑️ Delete
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="comment-text">
                        <?php echo nl2br(htmlspecialchars($reply['comment'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
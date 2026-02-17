<?php
/**
 * Subscription Functions for StreamIt
 * Handles all subscription-related functionality
 */

/**
 * Check if user has an active subscription
 */
function hasActiveSubscription($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT has_subscription, subscription_end_date 
        FROM users 
        WHERE id = ? AND has_subscription = 1
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return false;
    }
    
    // Check if subscription hasn't expired
    if ($user['subscription_end_date'] && strtotime($user['subscription_end_date']) > time()) {
        return true;
    }
    
    // Subscription expired, update status
    if ($user['subscription_end_date'] && strtotime($user['subscription_end_date']) <= time()) {
        expireUserSubscription($userId);
    }
    
    return false;
}

/**
 * Get user subscription details
 */
function getUserSubscription($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            u.has_subscription,
            u.subscription_type,
            u.subscription_start_date,
            u.subscription_end_date,
            DATEDIFF(u.subscription_end_date, NOW()) as days_remaining,
            sh.plan_id,
            sp.name as plan_name,
            sp.price,
            sh.status
        FROM users u
        LEFT JOIN subscription_history sh ON u.id = sh.user_id AND sh.status = 'active'
        LEFT JOIN subscription_plans sp ON sh.plan_id = sp.id
        WHERE u.id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Get all subscription plans
 */
function getSubscriptionPlans() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT * FROM subscription_plans 
        WHERE is_active = 1 
        ORDER BY duration_days ASC
    ");
    return $stmt->fetchAll();
}

/**
 * Get a specific subscription plan
 */
function getSubscriptionPlan($planId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM subscription_plans WHERE id = ?");
    $stmt->execute([$planId]);
    return $stmt->fetch();
}

/**
 * Activate user subscription
 */
function activateSubscription($userId, $planId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get plan details
        $plan = getSubscriptionPlan($planId);
        if (!$plan) {
            throw new Exception("Invalid subscription plan");
        }
        
        // Calculate dates
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));
        
        // Determine subscription type
        $subscriptionType = ($plan['duration_days'] >= 365) ? 'yearly' : 'monthly';
        
        // Update user subscription status
        $stmt = $pdo->prepare("
            UPDATE users 
            SET has_subscription = 1,
                subscription_type = ?,
                subscription_start_date = ?,
                subscription_end_date = ?
            WHERE id = ?
        ");
        $stmt->execute([$subscriptionType, $startDate, $endDate, $userId]);
        
        // Add to subscription history
        $stmt = $pdo->prepare("
            INSERT INTO subscription_history 
            (user_id, plan_id, amount, payment_method, status, start_date, end_date)
            VALUES (?, ?, ?, 'manual', 'active', ?, ?)
        ");
        $stmt->execute([$userId, $planId, $plan['price'], $startDate, $endDate]);
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Subscription activation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Cancel user subscription (immediate)
 */
function cancelSubscription($userId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Update user subscription status
        $stmt = $pdo->prepare("
            UPDATE users 
            SET has_subscription = 0,
                subscription_type = 'free',
                subscription_end_date = NULL
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        
        // Update subscription history
        $stmt = $pdo->prepare("
            UPDATE subscription_history 
            SET status = 'cancelled',
                end_date = NOW()
            WHERE user_id = ? AND status = 'active'
        ");
        $stmt->execute([$userId]);
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Subscription cancellation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Expire user subscription (when time runs out)
 */
function expireUserSubscription($userId) {
    global $pdo;
    
    try {
        // Update user subscription status
        $stmt = $pdo->prepare("
            UPDATE users 
            SET has_subscription = 0,
                subscription_type = 'free'
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        
        // Update subscription history
        $stmt = $pdo->prepare("
            UPDATE subscription_history 
            SET status = 'expired'
            WHERE user_id = ? AND status = 'active' AND end_date <= NOW()
        ");
        $stmt->execute([$userId]);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Subscription expiration error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user can upload subscriber-only videos
 */
function canUploadSubscriberVideos($userId) {
    return hasActiveSubscription($userId);
}

/**
 * Check if user can view a video
 */
function canViewVideo($videoId, $userId = null) {
    global $pdo;
    
    // Get video details
    $stmt = $pdo->prepare("SELECT user_id, is_subscriber_only, status FROM videos WHERE id = ?");
    $stmt->execute([$videoId]);
    $video = $stmt->fetch();
    
    if (!$video || $video['status'] !== 'approved') {
        return false;
    }
    
    // If video is not subscriber-only, anyone can view
    if (!$video['is_subscriber_only']) {
        return true;
    }
    
    // If video is subscriber-only, check user's subscription
    if (!$userId) {
        return false; // Not logged in, can't view subscriber-only
    }
    
    // Video owner can always view their own videos
    if ($userId == $video['user_id']) {
        return true;
    }
    
    // Check if user has active subscription
    return hasActiveSubscription($userId);
}

/**
 * Get videos filtered by subscription access
 */
function getVideosWithSubscriptionFilter($status = 'approved', $category = null, $search = null, $limit = null, $offset = 0, $userId = null) {
    global $pdo;
    
    $sql = "SELECT v.*, u.username, c.name as category_name,
            u.has_subscription as uploader_has_subscription
            FROM videos v 
            JOIN users u ON v.user_id = u.id 
            JOIN categories c ON v.category_id = c.id 
            WHERE v.status = ?";
    
    $params = [$status];
    
    // Filter based on user subscription status
    if (!$userId || !hasActiveSubscription($userId)) {
        // Non-subscribers can only see free videos
        $sql .= " AND v.is_subscriber_only = 0";
    }
    // Subscribers can see all videos
    
    if ($category) {
        $sql .= " AND v.category_id = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (v.title LIKE ? OR v.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY v.upload_date DESC";
    
    if ($limit) {
        $sql .= " LIMIT $limit OFFSET $offset";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Count videos with subscription filter
 */
function countVideosWithSubscriptionFilter($search = '', $category = null, $userId = null) {
    global $pdo;
    
    $sql = "SELECT COUNT(*) as total FROM videos v WHERE v.status = 'approved'";
    $params = [];
    
    // Filter based on user subscription status
    if (!$userId || !hasActiveSubscription($userId)) {
        $sql .= " AND v.is_subscriber_only = 0";
    }
    
    if ($search) {
        $sql .= " AND (v.title LIKE ? OR v.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($category) {
        $sql .= " AND v.category_id = ?";
        $params[] = $category;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'];
}

/**
 * Get subscription statistics (for admin)
 */
function getSubscriptionStats() {
    global $pdo;
    
    $stats = [];
    
    // Total active subscriptions
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE has_subscription = 1 AND subscription_end_date > NOW()
    ");
    $stats['active_subscriptions'] = $stmt->fetch()['count'];
    
    // Total expired subscriptions
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM users 
        WHERE has_subscription = 0 AND subscription_end_date IS NOT NULL
    ");
    $stats['expired_subscriptions'] = $stmt->fetch()['count'];
    
    // Total revenue (from subscription history)
    $stmt = $pdo->query("
        SELECT SUM(amount) as total 
        FROM subscription_history 
        WHERE status IN ('active', 'expired')
    ");
    $stats['total_revenue'] = $stmt->fetch()['total'] ?: 0;
    
    // Subscriber-only videos count
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM videos 
        WHERE is_subscriber_only = 1 AND status = 'approved'
    ");
    $stats['subscriber_videos'] = $stmt->fetch()['count'];
    
    return $stats;
}

/**
 * Grant admin subscription to user
 */
function grantAdminSubscription($userId, $days = 30) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $startDate = date('Y-m-d H:i:s');
        $endDate = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        $subscriptionType = ($days >= 365) ? 'yearly' : 'monthly';
        
        // Update user
        $stmt = $pdo->prepare("
            UPDATE users 
            SET has_subscription = 1,
                subscription_type = ?,
                subscription_start_date = ?,
                subscription_end_date = ?
            WHERE id = ?
        ");
        $stmt->execute([$subscriptionType, $startDate, $endDate, $userId]);
        
        // Add to history
        $stmt = $pdo->prepare("
            INSERT INTO subscription_history 
            (user_id, plan_id, amount, payment_method, status, start_date, end_date)
            VALUES (?, 1, 0.00, 'admin_grant', 'active', ?, ?)
        ");
        $stmt->execute([$userId, $startDate, $endDate]);
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Admin subscription grant error: " . $e->getMessage());
        return false;
    }
}

/**
 * Format subscription end date for display
 */
function formatSubscriptionDate($date) {
    if (!$date) {
        return 'N/A';
    }
    return date('F j, Y', strtotime($date));
}

/**
 * Get days remaining in subscription
 */
function getSubscriptionDaysRemaining($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT DATEDIFF(subscription_end_date, NOW()) as days_remaining
        FROM users
        WHERE id = ? AND has_subscription = 1
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    return $result ? max(0, $result['days_remaining']) : 0;
}
?>
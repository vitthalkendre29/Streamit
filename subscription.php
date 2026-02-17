<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/subscription_functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$user = getUserInfo($userId);
$subscription = getUserSubscription($userId);
$plans = getSubscriptionPlans();

$message = '';
$messageType = '';

// Handle subscription actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'activate':
                $planId = (int)$_POST['plan_id'];
                if (activateSubscription($userId, $planId)) {
                    $message = 'Subscription activated successfully!';
                    $messageType = 'success';
                    // Refresh subscription data
                    $subscription = getUserSubscription($userId);
                } else {
                    $message = 'Failed to activate subscription. Please try again.';
                    $messageType = 'error';
                }
                break;
                
            case 'cancel':
                if (cancelSubscription($userId)) {
                    $message = 'Subscription cancelled successfully.';
                    $messageType = 'success';
                    // Refresh subscription data
                    $subscription = getUserSubscription($userId);
                } else {
                    $message = 'Failed to cancel subscription. Please try again.';
                    $messageType = 'error';
                }
                break;
        }
    }
}

$hasActiveSubscription = hasActiveSubscription($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Subscription - StreamIt</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .subscription-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }
        
        .subscription-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            color: white;
        }
        
        .subscription-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
        }
        
        .subscription-status {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        
        .status-card {
            background: #2d3436;
            padding: 1.5rem;
            border-radius: 10px;
            flex: 1;
            min-width: 250px;
        }
        
        .status-card h3 {
            color: white;
            margin: 0 0 1rem 0;
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .status-card .value {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .status-card.active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .status-card.inactive {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        
        .plans-section {
            margin-bottom: 2rem;
        }
        
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }
        
        .plan-card {
            background: #2d3436;
            border: 2px solid #444;
            border-radius: 10px;
            padding: 2rem;
            transition: all 0.3s;
        }
        
        .plan-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
        }
        
        .plan-card.featured {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        
        .plan-header {
            border-bottom: 2px solid #444;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .plan-name {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0 0 0.5rem 0;
        }
        
        .plan-price {
            color: #667eea;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .plan-duration {
            color: #888;
            font-size: 0.9rem;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }
        
        .plan-features li {
            color: #ccc;
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }
        
        .plan-features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        .plan-action {
            margin-top: 1.5rem;
        }
        
        .current-plan {
            background: #2d3436;
            border: 2px solid #667eea;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .benefits-section {
            background: #2d3436;
            padding: 2rem;
            border-radius: 10px;
            margin-top: 2rem;
        }
        
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .benefit-card {
            background: #1e272e;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .benefit-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .benefit-title {
            color: white;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .benefit-description {
            color: #888;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="subscription-container">
        <div class="subscription-header">
            <h1>Subscription Management</h1>
            <p>Upgrade to access exclusive subscriber-only content</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Subscription Status -->
        <div class="subscription-status">
            <div class="status-card <?php echo $hasActiveSubscription ? 'active' : 'inactive'; ?>">
                <h3>Current Status</h3>
                <div class="value">
                    <?php echo $hasActiveSubscription ? 'Active Subscriber' : 'Free User'; ?>
                </div>
            </div>
            
            <?php if ($hasActiveSubscription): ?>
                <div class="status-card">
                    <h3>Subscription Type</h3>
                    <div class="value"><?php echo ucfirst($subscription['subscription_type']); ?></div>
                </div>
                
                <div class="status-card">
                    <h3>Days Remaining</h3>
                    <div class="value"><?php echo max(0, $subscription['days_remaining']); ?> days</div>
                </div>
                
                <div class="status-card">
                    <h3>Expires On</h3>
                    <div class="value"><?php echo date('M j, Y', strtotime($user['subscription_end_date'])); ?></div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Current Plan (if subscribed) -->
        <?php if ($hasActiveSubscription): ?>
            <div class="current-plan">
                <h2 style="color: white; margin-bottom: 1rem;">Current Plan</h2>
                <p style="color: #ccc;">
                    You are currently subscribed to <strong><?php echo htmlspecialchars($subscription['plan_name'] ?: 'Custom Plan'); ?></strong>
                </p>
                <p style="color: #888; margin: 1rem 0;">
                    Your subscription will renew on <?php echo formatSubscriptionDate($user['subscription_end_date']); ?>
                </p>
                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="btn btn-secondary">Cancel Subscription</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Available Plans -->
        <?php if (!$hasActiveSubscription): ?>
            <div class="plans-section">
                <h2 style="color: white; margin-bottom: 1rem;">Choose Your Plan</h2>
                <div class="plans-grid">
                    <?php foreach ($plans as $index => $plan): ?>
                        <div class="plan-card <?php echo $index === 1 ? 'featured' : ''; ?>">
                            <?php if ($index === 1): ?>
                                <div style="background: #667eea; color: white; padding: 0.5rem; text-align: center; margin: -2rem -2rem 1rem -2rem; border-radius: 8px 8px 0 0; font-weight: bold;">
                                    BEST VALUE
                                </div>
                            <?php endif; ?>
                            
                            <div class="plan-header">
                                <div class="plan-name"><?php echo htmlspecialchars($plan['name']); ?></div>
                                <div class="plan-price">$<?php echo number_format($plan['price'], 2); ?></div>
                                <div class="plan-duration">per <?php echo $plan['duration_days'] >= 365 ? 'year' : 'month'; ?></div>
                            </div>
                            
                            <ul class="plan-features">
                                <li>Access to all subscriber-only videos</li>
                                <li>Upload subscriber-only content</li>
                                <li>Ad-free viewing experience</li>
                                <li>Priority support</li>
                                <li>Early access to new features</li>
                                <?php if ($plan['duration_days'] >= 365): ?>
                                    <li>Save 17% compared to monthly</li>
                                <?php endif; ?>
                            </ul>
                            
                            <div class="plan-action">
                                <form method="POST">
                                    <input type="hidden" name="action" value="activate">
                                    <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                                    <button type="submit" class="btn btn-primary btn-full">
                                        Subscribe Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Benefits Section -->
        <div class="benefits-section">
            <h2 style="color: white; margin-bottom: 1rem;">Subscriber Benefits</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">🎥</div>
                    <div class="benefit-title">Exclusive Content</div>
                    <div class="benefit-description">
                        Access premium subscriber-only videos from your favorite creators
                    </div>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">📤</div>
                    <div class="benefit-title">Upload Premium Videos</div>
                    <div class="benefit-description">
                        Create and share subscriber-only content with your audience
                    </div>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">🚫</div>
                    <div class="benefit-title">Ad-Free Experience</div>
                    <div class="benefit-description">
                        Enjoy uninterrupted viewing without advertisements
                    </div>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">⚡</div>
                    <div class="benefit-title">Early Access</div>
                    <div class="benefit-description">
                        Be the first to try new features and updates
                    </div>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">💬</div>
                    <div class="benefit-title">Priority Support</div>
                    <div class="benefit-description">
                        Get faster responses and dedicated customer support
                    </div>
                </div>
                
                <div class="benefit-card">
                    <div class="benefit-icon">🎯</div>
                    <div class="benefit-title">Full Platform Access</div>
                    <div class="benefit-description">
                        Unlock all features and capabilities of StreamIt
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 StreamIt. A secure, private video streaming platform.</p>
    </div>

    <script src="./js/main.js"></script>
</body>
</html>
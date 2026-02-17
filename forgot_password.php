<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/email_functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Check if this is a token-based reset
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (!empty($token)) {
    // Validate token
    $stmt = $pdo->prepare("
        SELECT pr.*, u.id as user_id, u.username, u.email as user_email 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.verified = 0
        LIMIT 1
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
    
    if (!$reset) {
        $error = 'This password reset link is invalid or has expired. Please request a new one.';
        $token = ''; // Clear token
    }
}

// Step 1: Request password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = sanitizeInput($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate secure token
            $token = bin2hex(random_bytes(32)); // 64 character token
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hour validity
            
            // Store token in database
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address) 
                    VALUES (?, ?, ?, ?, NOW(), ?)
                    ON DUPLICATE KEY UPDATE 
                    token = VALUES(token), 
                    expires_at = VALUES(expires_at), 
                    created_at = NOW(),
                    verified = 0,
                    ip_address = VALUES(ip_address)
                ");
                $stmt->execute([
                    $user['id'], 
                    $email, 
                    $token,
                    $expiry,
                    $_SERVER['REMOTE_ADDR']
                ]);
                
                // Create reset link
                $reset_link = SITE_URL . "forgot_password.php?token=" . $token;
                
                // Send email with reset link
                try {
                    sendPasswordResetLinkEmail($email, $user['username'], $reset_link);
                    error_log("Password reset link sent to $email");
                } catch (Exception $e) {
                    error_log("Email error: " . $e->getMessage());
                }
                
                $success = 'A password reset link has been sent to your email address. Please check your inbox (or Mailtrap).';
                
            } catch (Exception $e) {
                $error = 'Database error. Please try again.';
                error_log("Database error: " . $e->getMessage());
            }
        } else {
            // Don't reveal if email exists
            $success = 'If an account with that email exists, a password reset link has been sent.';
        }
    }
}

// Step 2: Reset password with token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = sanitizeInput($_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Verify token again
        $stmt = $pdo->prepare("
            SELECT pr.*, u.id as user_id, u.username, u.email as user_email 
            FROM password_resets pr 
            JOIN users u ON pr.user_id = u.id 
            WHERE pr.token = ?  AND pr.verified = 0
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();
        
        if ($reset) {
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_result = $stmt->execute([$hashedPassword, $reset['user_id']]);
            
            if ($update_result) {
                // Mark token as used
                $stmt = $pdo->prepare("UPDATE password_resets SET verified = 1 WHERE id = ?");
                $stmt->execute([$reset['id']]);
                
                // Delete old reset records for this user
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ? AND id != ?");
                $stmt->execute([$reset['user_id'], $reset['id']]);
                
                // Send confirmation email
                try {
                    sendPasswordResetConfirmationEmail($reset['user_email']);
                } catch (Exception $e) {
                    error_log("Confirmation email failed: " . $e->getMessage());
                }
                
                // Redirect to login with success message
                $_SESSION['password_reset_success'] = true;
                header('Location: login.php?reset=success');
                exit;
            } else {
                $error = 'Failed to update password. Please try again.';
            }
        } else {
            $error = 'This password reset link is invalid or has expired. Please request a new one.';
            $token = ''; // Clear token
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - StreamIt</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        .info-box {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #0c5460;
        }
        
        .info-box a {
            color: #0c5460;
            font-weight: bold;
        }
        
        .security-info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="auth-form">
            <?php if (empty($token)): ?>
                <!-- Step 1: Request Password Reset -->
                <h2>Reset Your Password</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    
                    <div class="info-box">
                        📧 <strong>Check your email inbox (or Mailtrap):</strong><br>
                        We've sent you a secure link to reset your password.<br>
                        <a href="https://mailtrap.io/inboxes" target="_blank">→ Open Mailtrap Inbox</a>
                    </div>
                    
                    <div class="security-info">
                        🔒 <strong>Security Note:</strong><br>
                        • The link will expire in 1 hour<br>
                        • For your security, you can only use it once<br>
                        • If you didn't request this, please ignore the email
                    </div>
                <?php endif; ?>
                
                <p style="text-align: center; color: #ccc; margin-bottom: 1.5rem;">
                    Enter your email address and we'll send you a secure link to reset your password.
                </p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required 
                               placeholder="your.email@example.com"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               autofocus>
                    </div>
                    
                    <button type="submit" name="request_reset" class="btn btn-primary btn-full">
                        📧 Send Password Reset Link
                    </button>
                </form>
                
            <?php else: ?>
                <!-- Step 2: Reset Password with Token -->
                <h2>Create New Password</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($reset): ?>
                    <div class="alert alert-success">
                        ✅ Link verified! You can now set a new password for <strong><?php echo htmlspecialchars($reset['user_email']); ?></strong>
                    </div>
                    
                    <p style="text-align: center; color: #ccc; margin-bottom: 1.5rem;">
                        Create a strong password with at least 8 characters.
                    </p>
                    
                    <form method="POST" action="" id="resetForm">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                        
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" required 
                                   minlength="8"
                                   placeholder="Enter new password"
                                   autofocus>
                            <div class="password-strength" id="strengthIndicator"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   placeholder="Confirm new password">
                        </div>
                        
                        <button type="submit" name="reset_password" class="btn btn-primary btn-full">
                            🔒 Reset Password
                        </button>
                    </form>
                    
                    <div class="security-info" style="margin-top: 20px;">
                        🔒 <strong>Password Tips:</strong><br>
                        • Use at least 8 characters<br>
                        • Include uppercase and lowercase letters<br>
                        • Add numbers and special characters<br>
                        • Don't reuse old passwords
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <p class="auth-link">
                Remember your password? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 StreamIt. A secure, private video streaming platform.</p>
    </div>

    <script src="./js/main.js"></script>
    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('strengthIndicator');
        
        if (passwordInput && strengthIndicator) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength++;
                if (password.length >= 12) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^a-zA-Z0-9]/.test(password)) strength++;
                
                if (password.length === 0) {
                    strengthIndicator.textContent = '';
                } else if (strength <= 2) {
                    strengthIndicator.textContent = 'Weak password';
                    strengthIndicator.className = 'password-strength strength-weak';
                } else if (strength <= 4) {
                    strengthIndicator.textContent = 'Medium strength';
                    strengthIndicator.className = 'password-strength strength-medium';
                } else {
                    strengthIndicator.textContent = 'Strong password';
                    strengthIndicator.className = 'password-strength strength-strong';
                }
            });
        }
        
        // Password match validation
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
            });
        }
    </script>
</body>
</html>
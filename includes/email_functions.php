<?php
/**
 * Email Functions for Password Reset
 * Configured for Mailtrap using PHPMailer
 * 
 * SETUP INSTRUCTIONS:
 * 1. Install PHPMailer: composer require phpmailer/phpmailer
 * 2. Get Mailtrap credentials from: https://mailtrap.io/inboxes
 * 3. Replace YOUR_MAILTRAP_USERNAME and YOUR_MAILTRAP_PASSWORD below (lines 29-30)
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer - adjust path if needed
// require dirname(__DIR__) . '/vendor/autoload.php';

// If you downloaded PHPMailer manually instead of using Composer, uncomment these:
require dirname(__DIR__) . '/vendor/PHPMailer/src/Exception.php';
require dirname(__DIR__) . '/vendor/PHPMailer/src/PHPMailer.php';
require dirname(__DIR__) . '/vendor/PHPMailer/src/SMTP.php';

/**
 * Configure PHPMailer with Mailtrap SMTP
 */
function getMailer() {
    $mail = new PHPMailer(true);
    
    try {
        // ==========================================
        // MAILTRAP SMTP CONFIGURATION
        // ⚠️ REPLACE THESE WITH YOUR CREDENTIALS
        // ==========================================
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'a902c437f823a8';  // ⚠️ REPLACE THIS - Get from Mailtrap dashboard
        $mail->Password   = 'cea4068e25ed93';  // ⚠️ REPLACE THIS - Get from Mailtrap dashboard
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525; // You can also use 25, 465, 587, or 2525
        
        // Enable verbose debug output (comment out in production)
        // $mail->SMTPDebug = 2; // Uncomment to see detailed SMTP communication
        
        // Default sender
        $mail->setFrom('noreply@streamit.local', 'StreamIt Platform');
        
        // Enable HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
    } catch (Exception $e) {
        error_log("Mailer configuration error: {$mail->ErrorInfo}");
    }
    
    return $mail;
}

/**
 * Send Password Reset Link Email
 * 
 * @param string $email User's email address
 * @param string $username User's username
 * @param string $reset_link Password reset link
 * @return bool True if sent successfully
 */
function sendPasswordResetLinkEmail($email, $username, $reset_link) {
    $mail = getMailer();
    
    try {
        // Recipients
        $mail->addAddress($email, $username);
        
        // Content
        $mail->Subject = 'StreamIt - Reset Your Password';
        
        // HTML email body
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f4f4f4;
                }
                .email-container {
                    background: #ffffff;
                    border-radius: 10px;
                    padding: 40px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                }
                .logo {
                    font-size: 36px;
                    font-weight: bold;
                    background: linear-gradient(45deg, #667eea, #764ba2);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-align: center;
                    margin-bottom: 10px;
                }
                .header {
                    text-align: center;
                    color: #333;
                    margin-bottom: 30px;
                }
                .content {
                    margin: 30px 0;
                }
                .button-container {
                    text-align: center;
                    margin: 40px 0;
                }
                .reset-button {
                    display: inline-block;
                    padding: 16px 40px;
                    background: linear-gradient(45deg, #667eea, #764ba2);
                    color: white;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    font-size: 16px;
                }
                .reset-button:hover {
                    background: linear-gradient(45deg, #5568d3, #653a8b);
                }
                .link-box {
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 5px;
                    padding: 15px;
                    margin: 20px 0;
                    word-break: break-all;
                }
                .link-box p {
                    margin: 5px 0;
                    font-size: 12px;
                    color: #666;
                }
                .link-box a {
                    color: #667eea;
                    text-decoration: none;
                }
                .warning-box {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 20px;
                    margin: 25px 0;
                    border-radius: 5px;
                }
                .warning-title {
                    font-weight: bold;
                    margin-bottom: 10px;
                    color: #856404;
                }
                .warning-box ul {
                    margin: 10px 0;
                    padding-left: 20px;
                }
                .warning-box li {
                    margin: 5px 0;
                    color: #856404;
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #e0e0e0;
                    color: #999;
                    font-size: 12px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='logo'>StreamIt</div>
                <h2 class='header'>Reset Your Password</h2>
                
                <div class='content'>
                    <p>Hello <strong>" . htmlspecialchars($username) . "</strong>,</p>
                    
                    <p>We received a request to reset your password for your StreamIt account. Click the button below to create a new password:</p>
                </div>
                
                <div class='button-container'>
                    <a href='" . htmlspecialchars($reset_link) . "' class='reset-button'>
                        🔒 Reset My Password
                    </a>
                </div>
                
                <div class='link-box'>
                    <p><strong>Or copy and paste this link into your browser:</strong></p>
                    <a href='" . htmlspecialchars($reset_link) . "'>" . htmlspecialchars($reset_link) . "</a>
                </div>
                
                <div class='warning-box'>
                    <div class='warning-title'>⚠️ Important Security Information:</div>
                    <ul>
                        <li>This link will <strong>expire in 1 hour</strong></li>
                        <li>You can only use this link <strong>once</strong></li>
                        <li><strong>Never share</strong> this link with anyone</li>
                        <li>If you didn't request this reset, please ignore this email</li>
                        <li>Your password will remain unchanged unless you click the link</li>
                    </ul>
                </div>
                
                <div class='content'>
                    <p style='margin-top: 20px;'>If you have any questions or concerns, please contact our support team.</p>
                    
                    <p style='margin-top: 20px; color: #666; font-size: 14px;'>
                        <strong>Note:</strong> This link was requested from IP address: " . htmlspecialchars($_SERVER['REMOTE_ADDR']) . "
                    </p>
                </div>
                
                <div class='footer'>
                    <p><strong>StreamIt</strong> - Your Personal Video Streaming Platform</p>
                    <p>&copy; 2025 StreamIt. All rights reserved.</p>
                    <p>This is an automated email. Please do not reply to this message.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Plain text alternative
        $mail->AltBody = "StreamIt Password Reset\n\n"
                       . "Hello $username,\n\n"
                       . "We received a request to reset your password.\n\n"
                       . "Click this link to reset your password:\n"
                       . "$reset_link\n\n"
                       . "This link will expire in 1 hour.\n\n"
                       . "If you didn't request this, please ignore this email.\n\n"
                       . "StreamIt Team";
        
        // Send email
        $result = $mail->send();
        
        // Log email activity
        logEmailActivity($email, 'password_reset_link', $result);
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Password reset link email failed: {$mail->ErrorInfo}");
        logEmailActivity($email, 'password_reset_link', false);
        return false;
    }
}

/**
 * Send OTP Email (Legacy - kept for compatibility)
 */
function sendOTPEmail($email, $username, $otp) {
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $username);
        $mail->Subject = 'StreamIt - Password Reset OTP';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background: #f9f9f9; border-radius: 10px; padding: 30px; }
                .logo { font-size: 32px; font-weight: bold; color: #667eea; text-align: center; }
                .otp-box { background: #fff; border: 2px solid #667eea; border-radius: 10px; padding: 20px; text-align: center; margin: 30px 0; }
                .otp-code { font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #667eea; font-family: monospace; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='logo'>StreamIt</div>
                <h2 style='text-align: center;'>Password Reset OTP</h2>
                <p>Hello <strong>" . htmlspecialchars($username) . "</strong>,</p>
                <p>Your OTP code is:</p>
                <div class='otp-box'>
                    <div class='otp-code'>" . htmlspecialchars($otp) . "</div>
                    <p style='margin: 10px 0 0 0; font-size: 14px;'>Valid for 15 minutes</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Your OTP code is: $otp\nValid for 15 minutes.";
        
        $result = $mail->send();
        logEmailActivity($email, 'otp', $result);
        return $result;
        
    } catch (Exception $e) {
        error_log("OTP email failed: {$mail->ErrorInfo}");
        logEmailActivity($email, 'otp', false);
        return false;
    }
}

/**
 * Send Password Reset Confirmation Email
 */
function sendPasswordResetConfirmationEmail($email) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT username FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) return false;
    
    $username = $user['username'];
    $mail = getMailer();
    
    try {
        $mail->addAddress($email, $username);
        $mail->Subject = 'StreamIt - Password Successfully Reset';
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
                .container { background: #f9f9f9; border-radius: 10px; padding: 30px; }
                .logo { font-size: 32px; font-weight: bold; color: #667eea; text-align: center; }
                .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
                .btn { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='logo'>StreamIt</div>
                <h2 style='text-align: center;'>Password Successfully Reset</h2>
                <div style='text-align: center; font-size: 64px;'>✅</div>
                <p>Hello <strong>" . htmlspecialchars($username) . "</strong>,</p>
                <div class='success'>
                    <strong>Success!</strong> Your password has been successfully reset.
                </div>
                <p><strong>Reset Details:</strong></p>
                <ul>
                    <li>Date: " . date('F j, Y, g:i a') . "</li>
                    <li>IP Address: " . $_SERVER['REMOTE_ADDR'] . "</li>
                </ul>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/streamit/login.php' class='btn'>Login Now</a>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Your password has been successfully reset.";
        
        $result = $mail->send();
        logEmailActivity($email, 'reset_confirmation', $result);
        return $result;
        
    } catch (Exception $e) {
        error_log("Confirmation email failed: {$mail->ErrorInfo}");
        logEmailActivity($email, 'reset_confirmation', false);
        return false;
    }
}

/**
 * Log Email Activity
 */
function logEmailActivity($email, $type, $success) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO email_logs (email, type, success, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $email,
            $type,
            $success ? 1 : 0,
            $_SERVER['REMOTE_ADDR']
        ]);
    } catch (Exception $e) {
        error_log("Email log failed: " . $e->getMessage());
    }
}

/**
 * Check OTP Rate Limit
 */
function checkOTPRateLimit($ip) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM password_resets 
        WHERE created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        AND ip_address = ?
    ");
    $stmt->execute([$ip]);
    $result = $stmt->fetch();
    
    return $result['count'] >= 5;
}
?>
<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/subscription_functions.php';

if (!isLoggedIn()) {
    header('Location: login.php?redirect=upload.php');
    exit;
}

$userId = $_SESSION['user_id'];
$hasSubscription = hasActiveSubscription($userId);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $categoryId = (int)$_POST['category'];
    $isSubscriberOnly = isset($_POST['subscriber_only']) ? 1 : 0;
    
    // Check if user can upload subscriber-only videos
    if ($isSubscriberOnly && !$hasSubscription) {
        $error = 'You need an active subscription to upload subscriber-only videos.';
    } elseif (empty($title) || empty($categoryId)) {
        $error = 'Please fill in all required fields.';
    } elseif (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select a video file to upload.';
    } else {
        $file = $_FILES['video'];
        
        // File validation
        $allowedTypes = ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/webm'];
        $maxSize = 100 * 1024 * 1024; // 100MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Invalid file type. Please upload MP4, AVI, MOV, WMV, or WebM files only.';
        } elseif ($file['size'] > $maxSize) {
            $error = 'File too large. Maximum file size is 100MB.';
        } else {
            // Upload file
            $uploadDir = 'uploads/videos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Save to database with subscription flag
                $stmt = $pdo->prepare("
                    INSERT INTO videos (user_id, title, description, filename, category_id, is_subscriber_only, status) 
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ");
                
                if ($stmt->execute([$userId, $title, $description, $fileName, $categoryId, $isSubscriberOnly])) {
                    if ($isSubscriberOnly) {
                        $success = 'Subscriber-only video uploaded successfully! It will be reviewed by administrators before being published.';
                    } else {
                        $success = 'Video uploaded successfully! It will be reviewed by administrators before being published.';
                    }
                    // Clear form
                    $title = $description = '';
                } else {
                    $error = 'Database error. Please try again.';
                    unlink($targetPath); // Remove uploaded file
                }
            } else {
                $error = 'Failed to upload file. Please try again.';
            }
        }
    }
}

$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video - StreamIt</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .subscription-notice {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .subscription-notice h3 {
            margin: 0 0 0.5rem 0;
        }
        
        .subscription-notice p {
            margin: 0.5rem 0;
        }
        
        .subscriber-only-option {
            background: #2d3436;
            border: 2px solid #444;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }
        
        .subscriber-only-option.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
        }
        
        .checkbox-label input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .checkbox-label input[type="checkbox"]:disabled {
            cursor: not-allowed;
        }
        
        .subscriber-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <?php if (!$hasSubscription): ?>
            <div class="subscription-notice">
                <h3>🌟 Upgrade to Upload Subscriber-Only Videos</h3>
                <p>Get a subscription to unlock the ability to create exclusive content for your subscribers!</p>
                <a href="subscription.php" class="btn btn-secondary" style="margin-top: 1rem;">View Subscription Plans</a>
            </div>
        <?php else: ?>
            <div class="subscription-notice">
                <h3>✅ You're a Subscriber!</h3>
                <p>You can now upload subscriber-only videos. Mark your videos as subscriber-only to make them exclusive.</p>
            </div>
        <?php endif; ?>
        
        <div class="upload-form">
            <h2>Upload Your Video</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                <div class="form-group">
                    <label for="video">Video File *</label>
                    <input type="file" id="video" name="video" accept="video/*" required>
                    <small>Supported formats: MP4, AVI, MOV, WMV, WebM. Maximum size: 100MB</small>
                </div>
                
                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" required maxlength="200"
                           value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" maxlength="1000"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                    <small id="charCount">0/1000 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Subscriber-Only Option -->
                <div class="subscriber-only-option <?php echo !$hasSubscription ? 'disabled' : ''; ?>">
                    <label class="checkbox-label">
                        <input 
                            type="checkbox" 
                            id="subscriber_only" 
                            name="subscriber_only" 
                            <?php echo !$hasSubscription ? 'disabled' : ''; ?>
                        >
                        <div>
                            <strong style="color: white;">
                                Make this video subscriber-only
                                <?php if ($hasSubscription): ?>
                                    <span class="subscriber-badge">SUBSCRIBER</span>
                                <?php endif; ?>
                            </strong>
                            <p style="color: #888; margin: 0.5rem 0 0 0; font-size: 0.9rem;">
                                <?php if ($hasSubscription): ?>
                                    Only users with active subscriptions will be able to view this video
                                <?php else: ?>
                                    🔒 Requires an active subscription. <a href="subscription.php" style="color: #667eea;">Upgrade now</a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </label>
                </div>
                
                <div class="upload-progress" id="uploadProgress" style="display: none;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-text" id="progressText">Uploading... 0%</div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full" id="submitBtn">Upload Video</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2025 StreamIt. A secure, private video streaming platform.</p>
    </div>

    <script src="./js/main.js"></script>
    <script>
        // Character counter for description
        document.getElementById('description').addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('charCount').textContent = count + '/1000 characters';
        });
        
        // Auto-fill title from filename
        document.getElementById('video').addEventListener('change', function() {
            const file = this.files[0];
            if (file && !document.getElementById('title').value) {
                const filename = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                const title = filename.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                document.getElementById('title').value = title;
            }
        });
        
        // Upload progress simulation (in real implementation, use XMLHttpRequest progress)
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const progressDiv = document.getElementById('uploadProgress');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
            progressDiv.style.display = 'block';
            
            // Simulate progress (replace with actual progress in production)
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                }
                
                document.getElementById('progressFill').style.width = progress + '%';
                document.getElementById('progressText').textContent = 'Uploading... ' + Math.round(progress) + '%';
            }, 500);
        });
    </script>
</body>
</html>
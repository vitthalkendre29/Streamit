<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        if ($_GET['action'] == 'approve') {
            $pdo->prepare("UPDATE videos SET status = 'approved' WHERE id = ?")->execute([$id]);
            $success = "Video approved successfully!";
        } elseif ($_GET['action'] == 'reject') {
            $pdo->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
            $success = "Video deleted successfully!";
        }
    } catch (Exception $e) {
        $error = "Error processing request.";
    }
}

$videos = $pdo->query("
    SELECT v.*, u.username, c.name as category_name 
    FROM videos v 
    JOIN users u ON v.user_id = u.id 
    LEFT JOIN categories c ON v.category_id = c.id 
    ORDER BY v.upload_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$pendingCount = count(array_filter($videos, function($v) { return $v['status'] == 'pending'; }));
$approvedCount = count(array_filter($videos, function($v) { return $v['status'] == 'approved'; }));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Videos</title>
    <link rel="stylesheet" href="../css/stylwadmin.css">
</head>
<body>
    <div class="container">
        <h1>📹 Manage Videos</h1>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>📊 Video Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?= count($videos) ?></span>
                    <span class="stat-label">Total Videos</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $pendingCount ?></span>
                    <span class="stat-label">Pending Review</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= $approvedCount ?></span>
                    <span class="stat-label">Approved</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= array_sum(array_column($videos, 'views')) ?></span>
                    <span class="stat-label">Total Views</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>🎬 Video List</h2>
            <?php if (empty($videos)): ?>
                <p style="text-align: center; color: #7f8c8d; padding: 20px;">No videos uploaded yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($videos as $video): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($video['title']) ?></strong></td>
                            <td><?= htmlspecialchars($video['username']) ?></td>
                            <td><?= htmlspecialchars($video['category_name'] ?: 'Uncategorized') ?></td>
                            <td>
                                <span class="status-badge status-<?= $video['status'] ?>">
                                    <?= ucfirst($video['status']) ?>
                                </span>
                            </td>
                            <td><?= number_format($video['views']) ?></td>
                            <td><?= date('M j, Y', strtotime($video['upload_date'])) ?></td>
                            <td>
                                <?php if ($video['status'] == 'pending'): ?>
                                    <a href="?action=approve&id=<?= $video['id'] ?>" 
                                       class="btn btn-success" 
                                       style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                       ✅ Approve
                                    </a>
                                    <a href="?action=reject&id=<?= $video['id'] ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 6px 12px; font-size: 12px;"
                                       onclick="return confirm('Are you sure you want to delete this video?')">
                                       ❌ Reject
                                    </a>
                                <?php else: ?>
                                    <a href="?action=reject&id=<?= $video['id'] ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 6px 12px; font-size: 12px;"
                                       onclick="return confirm('Are you sure you want to delete this video?')">
                                       🗑️ Delete
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <a href="index.php" class="back-link">← Back to Dashboard</a>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
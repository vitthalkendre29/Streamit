<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Users</title>
    <link rel="stylesheet" href="../css/stylwadmin.css">
</head>
<body>
    <div class="container">
        <h1>👥 Registered Users</h1>

        <div class="card">
            <h2>📊 User Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?= count($users) ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= count(array_filter($users, function($u) { return strtotime($u['created_at']) > strtotime('-30 days'); })) ?></span>
                    <span class="stat-label">New This Month</span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>👤 User List</h2>
            <?php if (empty($users)): ?>
                <p style="text-align: center; color: #7f8c8d; padding: 20px;">No users registered yet.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                            <td>
                                <?php if (strtotime($user['created_at']) > strtotime('-7 days')): ?>
                                    <span class="status-badge status-approved">New</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">Active</span>
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
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $desc = sanitizeInput($_POST['description']);
    
    if ($name && $desc) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $desc]);
            $success = "Category added successfully!";
        } catch (Exception $e) {
            $error = "Error adding category. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $success = "Category deleted successfully!";
    } catch (Exception $e) {
        $error = "Error deleting category.";
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Categories</title>
    <link rel="stylesheet" href="../css/stylwadmin.css">
</head>
<body>
    <div class="container">
        <h1>📂 Manage Categories</h1>

        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>➕ Add New Category</h2>
            <form method="POST">
                <input type="text" name="name" placeholder="Category Name" required>
                <textarea name="description" placeholder="Category Description" required></textarea>
                <button type="submit" class="btn btn-success">Add Category</button>
            </form>
        </div>

        <div class="card">
            <h2>📋 Existing Categories</h2>
            <?php if (empty($categories)): ?>
                <p style="text-align: center; color: #7f8c8d; padding: 20px;">No categories found. Add your first category above!</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td>
                                <a href="?delete=<?= $cat['id'] ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this category?')"
                                   style="padding: 8px 15px; font-size: 14px;">
                                   🗑️ Delete
                                </a>
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
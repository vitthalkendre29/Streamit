<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/stylwadmin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h1>🔐 Admin Login</h1>
            
            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="text" name="username" placeholder="👤 Username" required>
                <input type="password" name="password" placeholder="🔒 Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <script src="../js/main.js"></script>
</body>
</html>
<?php
require_once 'session.php';

?>
<script src="main.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorsport Community Hub</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/style.css">
</head>
<body>
<nav>
    <div class="nav-container">
        <a href="dashboard.php" class="site-title">Motorsport Community Hub</a>
        
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="news.php">News</a>
            <a href="forums.php">Forums</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
                <div class="user-section">
                    <span class="welcome-text">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="logout.php">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
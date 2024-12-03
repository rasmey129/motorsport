<?php
// First, make sure session is included
require_once 'session.php';

// You might want to define a base URL
define('BASE_URL', '/motorsport');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorsport Community Hub</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <nav>
        <div class="nav-container">
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>/index.php">Motorsport Community Hub</a>
            </div>
            <div class="nav-links">
                <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
                <a href="<?php echo BASE_URL; ?>/news.php">News</a>
                <a href="<?php echo BASE_URL; ?>/forums.php">Forums</a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/dashboard.php">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/profile.php">Profile</a>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login.php">Login</a>
                    <a href="<?php echo BASE_URL; ?>/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
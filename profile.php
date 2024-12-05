<?php
require_once 'session.php';
require_once 'database.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

include 'header.php';
?>
<div class="container">
    <h1>Profile</h1>
    <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
</div>

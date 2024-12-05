<?php
require_once 'session.php';
require_once 'database.php';

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header('Location: forums.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT posts.*, users.username, categories.name as category_name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    JOIN categories ON posts.category_id = categories.id
    WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

include 'header.php';
?>
<div class="container">
    <?php if ($post): ?>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p>By <?php echo htmlspecialchars($post['username']); ?> 
           in <?php echo htmlspecialchars($post['category_name']); ?></p>
        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
    <?php else: ?>
        <p>Post not found.</p>
    <?php endif; ?>
</div>

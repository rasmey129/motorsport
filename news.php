<?php
require_once 'session.php';
require_once 'database.php';

$stmt = $pdo->query("
    SELECT posts.*, users.username, categories.name as category_name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    JOIN categories ON posts.category_id = categories.id
    WHERE categories.name = 'News' 
    ORDER BY posts.created_at DESC
");
$news = $stmt->fetchAll();

include 'header.php';
?>
<div class="container">
    <h1>Latest News</h1>
    <?php foreach ($news as $article): ?>
        <div class="news-item">
            <h2><?php echo htmlspecialchars($article['title']); ?></h2>
            <p>By <?php echo htmlspecialchars($article['username']); ?></p>
            <div class="content">
                <?php echo htmlspecialchars($article['content']); ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

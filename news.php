<?php
require_once 'session.php';
require_once 'database.php';

$stmt = $pdo->query("SELECT posts.*, users.username, categories.name as category_name 
                     FROM posts 
                     JOIN users ON posts.user_id = users.id 
                     JOIN categories ON posts.category_id = categories.id
                     WHERE categories.name = 'News' 
                     ORDER BY posts.created_at DESC");
$news = $stmt->fetchAll();

include 'header.php';
?>

<div class="container">
    <h1>Latest Motorsport News</h1>
    
    <div class="news-grid">
        <?php foreach ($news as $article): ?>
            <div class="news-card">
                <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                <p class="meta">
                    Posted by <?php echo htmlspecialchars($article['username']); ?> 
                    on <?php echo date('F j, Y', strtotime($article['created_at'])); ?>
                </p>
                <div class="content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
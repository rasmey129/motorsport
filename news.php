<?php
require_once 'session.php';
require_once 'database.php';

// Fetch news articles
$query = "
    SELECT 
        n.*,
        u.username as author
    FROM news n
    JOIN users u ON n.author_id = u.id
    ORDER BY n.created_at DESC
";

$stmt = $pdo->query($query);
$news = $stmt->fetchAll();

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorsport News</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="news-header">
            <h1>Latest Motorsport News</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create_news.php" class="create-news-btn">Create News Article</a>
            <?php endif; ?>
        </div>

        <div class="news-grid">
            <?php foreach ($news as $article): ?>
                <article class="news-card">
                    <?php if (!empty($article['image_url'])): ?>
                        <img src="<?= htmlspecialchars($article['image_url']) ?>" 
                             alt="<?= htmlspecialchars($article['title']) ?>" 
                             class="news-image">
                    <?php endif; ?>
                    <div class="news-content">
                        <h2><?= htmlspecialchars($article['title']) ?></h2>
                        <p class="news-meta">
                            By <?= htmlspecialchars($article['author']) ?> 
                            on <?= date('F j, Y', strtotime($article['created_at'])) ?>
                        </p>
                        <p class="news-excerpt">
                            <?= nl2br(htmlspecialchars(substr($article['content'], 0, 200))) ?>...
                        </p>
                        <a href="news_article.php?id=<?= $article['id'] ?>" class="read-more">Read More</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
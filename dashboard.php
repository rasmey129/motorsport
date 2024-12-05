<?php
require_once 'session.php';
require_once 'database.php';

$posts_query = "
    SELECT 
        p.*,
        u.username,
        c.name as category_name,
        COUNT(DISTINCT com.id) as comment_count
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN categories c ON p.category_id = c.id
    LEFT JOIN comments com ON p.id = com.post_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 5
";

$news_query = "
    SELECT 
        n.*,
        u.username as author
    FROM news n
    JOIN users u ON n.author_id = u.id
    ORDER BY n.created_at DESC
    LIMIT 3
";

$posts = $pdo->query($posts_query)->fetchAll();
$news = $pdo->query($news_query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Motorsport Community Hub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="dashboard-header">
            <h1>Motorsport Community Hub</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dashboard-actions">
                    <a href="create_post.php" class="action-btn">Create Post</a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="create_news.php" class="action-btn">Create News</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-grid">
            <section class="news-section">
                <div class="section-header">
                    <h2>Latest News</h2>
                    <a href="news.php" class="view-all">View All News</a>
                </div>
                <div class="news-cards">
                    <?php foreach ($news as $article): ?>
                        <article class="news-card">
                            <?php if (!empty($article['image_url'])): ?>
                                <img src="<?= htmlspecialchars($article['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($article['title']) ?>" 
                                     class="news-image">
                            <?php endif; ?>
                            <div class="news-content">
                                <h3><?= htmlspecialchars($article['title']) ?></h3>
                                <p class="news-meta">
                                    By <?= htmlspecialchars($article['author']) ?> 
                                    on <?= date('F j, Y', strtotime($article['created_at'])) ?>
                                </p>
                                <a href="news_article.php?id=<?= $article['id'] ?>" class="read-more">Read More</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="forum-section">
                <div class="section-header">
                    <h2>Recent Forum Posts</h2>
                    <a href="forums.php" class="view-all">View All Posts</a>
                </div>
                <?php foreach ($posts as $post): ?>
                    <div class="forum-post-card">
                        <h3>
                            <a href="post.php?id=<?= $post['id'] ?>">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h3>
                        <p class="post-meta">
                            Posted by <?= htmlspecialchars($post['username']) ?>
                            in <?= htmlspecialchars($post['category_name']) ?>
                            • <?= $post['comment_count'] ?> comments
                        </p>
                        <div class="post-preview">
                            <?= nl2br(htmlspecialchars(substr($post['content'], 0, 150))) ?>...
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        </div>
    </div>
</body>
</html>
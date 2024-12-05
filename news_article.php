<?php
require_once 'session.php';
require_once 'database.php';

$article_id = $_GET['id'] ?? null;
if (!$article_id) {
    header('Location: news.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT 
        news.*,
        users.username as author
    FROM news
    JOIN users ON news.author_id = users.id
    WHERE news.id = ?
");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $article['author_id']) {
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$article_id]);
        header('Location: news.php');
        exit();
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $article ? htmlspecialchars($article['title']) : 'Article Not Found' ?> - Motorsport News</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php if ($article): ?>
            <article class="news-article">
                <div class="article-header">
                    <h1><?= htmlspecialchars($article['title']) ?></h1>
                    
                    <div class="article-meta">
                        <span class="article-author">By <?= htmlspecialchars($article['author']) ?></span>
                        <span class="article-date"><?= date('F j, Y', strtotime($article['created_at'])) ?></span>
                    </div>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $article['author_id']): ?>
                        <div class="article-actions">
                            <a href="edit_news.php?id=<?= $article['id'] ?>" class="edit-btn">Edit Article</a>
                            <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn">Delete Article</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($article['image_url'])): ?>
                    <div class="article-image">
                        <img src="<?= htmlspecialchars($article['image_url']) ?>" 
                             alt="<?= htmlspecialchars($article['title']) ?>">
                    </div>
                <?php endif; ?>

                <div class="article-content">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </div>

                <div class="article-footer">
                    <a href="news.php" class="back-btn">Back to News</a>
                </div>
            </article>
        <?php else: ?>
            <div class="error-message">
                <h2>Article Not Found</h2>
                <p>The article you're looking for doesn't exist or has been removed.</p>
                <a href="news.php" class="back-btn">Back to News</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
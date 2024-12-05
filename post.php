<?php
require_once 'session.php';
require_once 'database.php';

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header('Location: forums.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        header('Location: forums.php');
        exit();
    } elseif (isset($_POST['comment'])) {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $_SESSION['user_id'], $_POST['comment']]);
        header("Location: post.php?id=" . $post_id);
        exit();
    }
}

$stmt = $pdo->prepare("
    SELECT 
        posts.*,
        users.username,
        categories.name as category_name,
        categories.id as category_id
    FROM posts
    JOIN users ON posts.user_id = users.id
    JOIN categories ON posts.category_id = categories.id
    WHERE posts.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT 
        comments.*,
        users.username
    FROM comments
    JOIN users ON comments.user_id = users.id
    WHERE post_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll();

$pageTitle = $post ? htmlspecialchars($post['title']) : 'Post Not Found';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Motorsport Community Hub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <?php if ($post): ?>
            <div class="post-container">
                <div class="post-header">
                    <div class="category-badge">
                        <?= htmlspecialchars($post['category_name']) ?>
                    </div>
                    <h1><?= htmlspecialchars($post['title']) ?></h1>
                    
                    <div class="post-meta">
                        <div class="author-info">
                            <span class="author-name">Posted by <?= htmlspecialchars($post['username']) ?></span>
                            <span class="post-date"><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $post['user_id']): ?>
                            <div class="post-actions">
                                <a href="edit_post.php?id=<?= $post['id'] ?>" class="edit-btn">Edit Post</a>
                                <form method="POST" class="delete-form" 
                                      onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="delete-btn">Delete Post</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="post-content">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <div class="comments-section">
                    <h2>Comments (<?= count($comments) ?>)</h2>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" class="comment-form">
                            <div class="form-group">
                                <textarea name="comment" rows="3" required 
                                         placeholder="Write your comment..."></textarea>
                            </div>
                            <button type="submit" class="comment-btn">Post Comment</button>
                        </form>
                    <?php else: ?>
                        <p class="login-prompt">Please <a href="login.php">login</a> to comment.</p>
                    <?php endif; ?>

                    <div class="comments-list">
                        <?php if (empty($comments)): ?>
                            <p class="no-comments">No comments yet. Be the first to comment!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <div class="comment-header">
                                        <span class="comment-author"><?= htmlspecialchars($comment['username']) ?></span>
                                        <span class="comment-date">
                                            <?= date('F j, Y g:i a', strtotime($comment['created_at'])) ?>
                                        </span>
                                    </div>
                                    <div class="comment-content">
                                        <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="post-footer">
                    <a href="forums.php" class="back-btn">Back to Forums</a>
                    <a href="<?= strtolower($post['category_name']) ?>.php" class="category-btn">
                        More from <?= htmlspecialchars($post['category_name']) ?>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>Post Not Found</h2>
                <p>The post you're looking for doesn't exist or has been removed.</p>
                <a href="forums.php" class="back-btn">Back to Forums</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
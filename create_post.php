<?php
require_once 'session.php';
require_once 'database.php';
requireLogin();

$posts = [];

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['category_id'])) {
        switch ($_POST['action']) {
            case 'create':
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, category_id, title, content) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $_POST['category_id'], $_POST['title'], $_POST['content']]);
                header('Location: dashboard.php');
                exit;
                break;
        }
    }
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT posts.*, categories.name as category_name
        FROM posts
        JOIN categories ON posts.category_id = categories.id
        WHERE posts.user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $posts = $stmt->fetchAll();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Post Dashboard</h1>
        
        <div class="dashboard-grid">
            <div class="create-post-section">
                <h2>Create New Post</h2>
                <form method="POST" class="post-form">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" required rows="6"></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Create Post</button>
                </form>
            </div>

            <div class="posts-section">
                <h2>Your Posts</h2>
                <?php if (empty($posts)): ?>
                    <p class="no-posts">You haven't created any posts yet.</p>
                <?php endif; ?>
                
                <?php foreach ($posts as $post): ?>
                    <div class="dashboard-post">
                        <h3><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="post-content"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <div class="post-meta">
                            Category: <?= htmlspecialchars($post['category_name']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
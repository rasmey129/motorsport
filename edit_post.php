<?php
require_once 'session.php';
require_once 'database.php';
requireLogin();

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    header('Location: forums.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name
    FROM posts p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.user_id = ?
");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: forums.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update') {
        $stmt = $pdo->prepare("
            UPDATE posts 
            SET title = ?, content = ?, category_id = ?
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['content'],
            $_POST['category_id'],
            $post_id,
            $_SESSION['user_id']
        ]);
        header('Location: post.php?id=' . $post_id);
        exit;
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$post_id, $_SESSION['user_id']]);
        header('Location: forums.php');
        exit;
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Post</h1>
        
        <div class="edit-post-container">
            <form method="POST" class="edit-post-form">
                <input type="hidden" name="action" value="update">
                
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" 
                           value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category_id" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" 
                                <?= $category['id'] == $post['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="10" required><?= 
                        htmlspecialchars($post['content']) 
                    ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="update-btn">Update Post</button>
                    <a href="post.php?id=<?= $post_id ?>" class="cancel-btn">Cancel</a>
                </div>
            </form>

            
            <form method="POST" class="delete-post-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="delete-btn">Delete Post</button>
            </form>
        </div>
    </div>
</body>
</html>
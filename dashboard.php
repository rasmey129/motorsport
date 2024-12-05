<?php
require_once 'session.php';
require_once 'database.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, category_id, title, content) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $_POST['category_id'], $_POST['title'], $_POST['content']]);
                break;
                
            case 'update':
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$_POST['title'], $_POST['content'], $_POST['category_id'], $_POST['post_id'], $_SESSION['user_id']]);
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
                $stmt->execute([$_POST['post_id'], $_SESSION['user_id']]);
                break;
        }
    }
}

// Fetch categories for dropdown
$stmt = $pdo->query("SELECT id, name FROM categories");
$categories = $stmt->fetchAll();

// Fetch user's posts
$stmt = $pdo->prepare("
    SELECT posts.*, categories.name as category_name 
    FROM posts 
    JOIN categories ON posts.category_id = categories.id 
    WHERE posts.user_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();

include 'header.php';
?>

<div class="container">
    <h1>Dashboard</h1>
    
    <div class="create-post">
        <h2>Create New Post</h2>
        <form id="createPostForm" method="POST" onsubmit="return validatePostForm()">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <button type="submit">Create Post</button>
        </form>
    </div>
    
    <div class="posts">
        <h2>Your Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <p class="meta">Category: <?php echo htmlspecialchars($post['category_name']); ?></p>
                <div class="actions">
                    <button onclick="editPost(<?php echo $post['id']; ?>)">Edit</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
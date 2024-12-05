<?php
require_once 'session.php';
require_once 'database.php';
requireLogin();

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['category_id'])) {
        switch ($_POST['action']) {
            case 'create':
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, category_id, title, content) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $_POST['category_id'], $_POST['title'], $_POST['content']]);
                break;
        }
    }
}

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
    
    <form method="POST">
        <input type="hidden" name="action" value="create">
        <div class="form-group">
            <label>Title: <input type="text" name="title" required></label>
        </div>
        <div class="form-group">
            <label>Category:
                <select name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>
        <div class="form-group">
            <label>Content: <textarea name="content" required></textarea></label>
        </div>
        <button type="submit">Create Post</button>
    </form>

    <div class="posts">
        <h2>Your Posts</h2>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <p><?php echo htmlspecialchars($post['content']); ?></p>
                <p>Category: <?php echo htmlspecialchars($post['category_name']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

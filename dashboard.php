<?php
require_once 'session.php';
requireLogin();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, category) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $_POST['title'], $_POST['content'], $_POST['category']]);
                break;
                
            case 'update':
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$_POST['title'], $_POST['content'], $_POST['category'], $_POST['post_id'], $_SESSION['user_id']]);
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
                $stmt->execute([$_POST['post_id'], $_SESSION['user_id']]);
                break;
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

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
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="F1">Formula 1</option>
                    <option value="MotoGP">MotoGP</option>
                    <option value="Rally">Rally</option>
                    <option value="Other">Other</option>
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
                <p class="meta">Category: <?php echo htmlspecialchars($post['category']); ?></p>
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

<script>
function validatePostForm() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    if (title.length < 5) {
        alert('Title must be at least 5 characters long');
        return false;
    }
    
    if (content.length < 20) {
        alert('Content must be at least 20 characters long');
        return false;
    }
    
    return true;
}

function editPost(postId) {
 
}
</script>
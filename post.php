<?php
require_once 'session.php';
require_once 'database.php';

$postId = $_GET['id'] ?? null;

if (!$postId) {
    header('Location: /forums.php');
    exit();
}

$stmt = $pdo->prepare("
    SELECT 
        posts.*,
        users.username,
        COUNT(DISTINCT comments.id) as comment_count,
        users.created_at as user_joined
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    LEFT JOIN comments ON posts.id = comments.post_id
    WHERE posts.id = ?
    GROUP BY posts.id
");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: /forums.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'comment':
                $content = trim($_POST['content']);
                if (!empty($content)) {
                    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
                    $stmt->execute([$postId, $_SESSION['user_id'], $content]);
                    header("Location: /post.php?id=$postId#comment-" . $pdo->lastInsertId());
                    exit();
                }
                break;
                
            case 'delete_comment':
                if (isset($_POST['comment_id'])) {
                    $stmt = $pdo->prepare("
                        DELETE FROM comments 
                        WHERE id = ? AND user_id = ? 
                        OR ? IN (SELECT id FROM users WHERE role = 'admin')
                    ");
                    $stmt->execute([$_POST['comment_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
                }
                header("Location: /post.php?id=$postId#comments");
                exit();
                break;
        }
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("
    SELECT 
        comments.*,
        users.username,
        users.created_at as user_joined,
        (SELECT COUNT(*) FROM comments WHERE user_id = users.id) as user_post_count
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE post_id = ? 
    ORDER BY comments.created_at ASC
    LIMIT ? OFFSET ?
");
$stmt->execute([$postId, $perPage, $offset]);
$comments = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
$stmt->execute([$postId]);
$totalComments = $stmt->fetchColumn();
$totalPages = ceil($totalComments / $perPage);
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="breadcrumb">
        <a href="/forums.php">Forums</a> &gt; 
        <a href="/forums.php?category=<?php echo urlencode($post['category']); ?>"><?php echo htmlspecialchars($post['category']); ?></a> &gt; 
        <span><?php echo htmlspecialchars($post['title']); ?></span>
    </div>

    <article class="post-full">
        <header class="post-header">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-meta">
                <div class="author-info">
                    <span class="author-name"><?php echo htmlspecialchars($post['username']); ?></span>
                    <span class="post-date">Posted <?php echo date('F j, Y \a\t g:i a', strtotime($post['created_at'])); ?></span>
                </div>
            </div>
        </header>

        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
        <div class="post-actions">
            <a href="/edit-post.php?id=<?php echo $post['id']; ?>" class="button">Edit Post</a>
            <form method="POST" action="/delete-post.php" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" class="button delete">Delete Post</button>
            </form>
        </div>
        <?php endif; ?>
    </article>

    <section class="comments-section" id="comments">
        <h2><?php echo $totalComments; ?> Comments</h2>

        <?php if (isLoggedIn()): ?>
        <div class="comment-form">
            <h3>Add a Comment</h3>
            <form id="commentForm" method="POST" onsubmit="return validateCommentForm()">
                <input type="hidden" name="action" value="comment">
                <div class="form-group">
                    <textarea id="content" name="content" rows="4" required 
                              placeholder="Write your comment here..."></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="button">Post Comment</button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="login-prompt">
            <p>Please <a href="/login.php">log in</a> or <a href="/register.php">register</a> to post comments.</p>
        </div>
        <?php endif; ?>

        <div class="comments-list">
            <?php foreach ($comments as $comment): ?>
            <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                <div class="comment-meta">
                    <div class="comment-author">
                        <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                        <span class="comment-date">
                            <?php echo date('F j, Y \a\t g:i a', strtotime($comment['created_at'])); ?>
                        </span>
                    </div>
                    <?php if (isLoggedIn() && ($comment['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
                    <div class="comment-actions">
                        <form method="POST" class="inline-form" onsubmit="return confirm('Delete this comment?');">
                            <input type="hidden" name="action" value="delete_comment">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                            <button type="submit" class="button small delete">Delete</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="comment-content">
                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?id=<?php echo $postId; ?>&page=<?php echo ($page - 1); ?>#comments" class="prev">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                <a href="?id=<?php echo $postId; ?>&page=<?php echo $i; ?>#comments"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?id=<?php echo $postId; ?>&page=<?php echo ($page + 1); ?>#comments" class="next">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>
</div>

<script>
function validateCommentForm() {
    const content = document.getElementById('content').value.trim();
    
    if (content.length < 2) {
        alert('Comment must be at least 2 characters long');
        return false;
    }
    
    if (content.length > 10000) {
        alert('Comment is too long (maximum 10000 characters)');
        return false;
    }
    
    return true;
}

document.getElementById('content')?.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
</script>
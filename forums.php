<?php
require_once 'session.php';
require_once 'database.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

$query = "SELECT posts.*, users.username, 
          (SELECT COUNT(*) FROM comments WHERE post_id = posts.id) as comment_count 
          FROM posts 
          JOIN users ON posts.user_id = users.id";

if ($category !== 'all') {
    $query .= " WHERE posts.category = ?";
}

$query .= " ORDER BY posts.created_at DESC";

$stmt = $pdo->prepare($query);
if ($category !== 'all') {
    $stmt->execute([$category]);
} else {
    $stmt->execute();
}

$posts = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Forums</h1>
    
    <div class="category-filter">
        <a href="/forums.php" class="<?php echo $category === 'all' ? 'active' : ''; ?>">All</a>
        <a href="/forums.php?category=F1" class="<?php echo $category === 'F1' ? 'active' : ''; ?>">Formula 1</a>
        <a href="/forums.php?category=MotoGP" class="<?php echo $category === 'MotoGP' ? 'active' : ''; ?>">MotoGP</a>
        <a href="/forums.php?category=Rally" class="<?php echo $category === 'WRC' ? 'active' : ''; ?>">Rally</a>
    </div>
    
    <?php if (isLoggedIn()): ?>
        <a href="/create-post.php" class="button">Create New Post</a>
    <?php endif; ?>
    
    <div class="forum-posts">
        <?php foreach ($posts as $post): ?>
            <div class="forum-post">
                <h2><a href="/post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <p class="meta">
                    Posted by <?php echo htmlspecialchars($post['username']); ?>
                    in <?php echo htmlspecialchars($post['category']); ?>
                    | <?php echo $post['comment_count']; ?> comments
                </p>
                <div class="preview">
                    <?php echo substr(htmlspecialchars($post['content']), 0, 200) . '...'; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

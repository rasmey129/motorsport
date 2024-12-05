<?php
require_once 'session.php';
require_once 'database.php';

// Initialize posts array
$posts = [];

$search = $_GET['search'] ?? '';

// Only fetch posts if there's a search query
if ($search) {
    $query = "
        SELECT
            p.*,
            u.username,
            c.name as category_name,
            COUNT(DISTINCT com.id) as comment_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        JOIN categories c ON p.category_id = c.id
        LEFT JOIN comments com ON p.id = com.post_id
        WHERE (p.title LIKE ? OR p.content LIKE ?)
        GROUP BY p.id 
        ORDER BY p.created_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute(["%$search%", "%$search%"]);
    $posts = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forums</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="forums-header">
            <h1>Forums</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create_post.php" class="create-post-btn">Create Post</a>
            <?php endif; ?>
        </div>

        <div class="category-grid">
            <a href="forums.php" class="category-card active">
                <h2>All Categories</h2>
                <p>View all forum posts across categories</p>
            </a>
            <a href="f1.php" class="category-card">
                <h2>Formula 1</h2>
                <p>Discuss F1 races, drivers, and teams</p>
            </a>
            <a href="motogp.php" class="category-card">
                <h2>MotoGP</h2>
                <p>MotoGP racing discussions and news</p>
            </a>
            <a href="wrc.php" class="category-card">
                <h2>WRC</h2>
                <p>World Rally Championship updates and discussions</p>
            </a>
            <a href="wec.php" class="category-card">
                <h2>WEC</h2>
                <p>World Endurance Championship topics</p>
            </a>
        </div>

        <form class="search-form" action="forums.php" method="GET">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search posts..." class="search-input">
            <button type="submit" class="search-button">Search</button>
        </form>

        <?php if ($search): ?>
            <p class="search-results">
                Search results for: "<?= htmlspecialchars($search) ?>"
            </p>
            
            <?php if (empty($posts)): ?>
                <p class="no-posts">No posts found.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="forum-post">
                        <h2>
                            <a href="post.php?id=<?= $post['id'] ?>">
                                <?= htmlspecialchars($post['title']) ?>
                            </a>
                        </h2>
                        <p class="post-meta">
                            Posted by <?= htmlspecialchars($post['username']) ?>
                            in <?= htmlspecialchars($post['category_name']) ?>
                            on <?= date('F j, Y', strtotime($post['created_at'])) ?>
                            • <?= $post['comment_count'] ?> comments
                        </p>
                        <div class="post-preview">
                            <?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
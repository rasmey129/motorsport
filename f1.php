<?php
require_once 'session.php';
require_once 'database.php';

$category_id = 1; 
$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();
$category_name = $category['name'] ?? 'WRC';

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
    WHERE p.category_id = ?
";

$params = [$category_id];

if ($search) {
    $query .= " AND (p.title LIKE ? OR p.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " GROUP BY p.id ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category_name) ?> Forums</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="forums-nav">
        <div class="nav-container">
            <div class="forum-categories">
                <a href="forums.php" class="nav-item">All Forums</a>
                <a href="f1.php" class="nav-item">Formula 1</a>
                <a href="motogp.php" class="nav-item">MotoGP</a>
                <a href="wrc.php" class="nav-item active">WRC</a>
                <a href="wec.php" class="nav-item">WEC</a>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create_post.php" class="create-post-nav">Create Post</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h1><?= htmlspecialchars($category_name) ?> Forums</h1>
        
        <form class="search-form" action="wrc.php" method="GET">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search posts..." class="search-input">
            <button type="submit" class="search-button">Search</button>
        </form>

        <?php if ($search): ?>
            <p class="search-results">
                Search results for: "<?= htmlspecialchars($search) ?>"
            </p>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <p class="no-posts">No posts found.</p>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
            <div class="forum-post">
                <h2>
                    <a href="post.php?id=<?= $post['id'] ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h2>
                <p class="post-meta">
                    Posted by <?= htmlspecialchars($post['username']) ?>
                    on <?= date('F j, Y', strtotime($post['created_at'])) ?>
                    • <?= $post['comment_count'] ?> comments
                </p>
                <div class="post-preview">
                    <?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="create_post.php" class="create-post-btn">Create New Post</a>
        <?php endif; ?>
    </div>
</body>
</html>
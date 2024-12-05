<?php
require_once 'session.php';
require_once 'database.php';

$category_id = $_GET['category'] ?? null;

$query = "
    SELECT posts.*, users.username, categories.name as category_name 
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    JOIN categories ON posts.category_id = categories.id
";

if ($category_id) {
    $query .= " WHERE posts.category_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query($query);
}

$posts = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

include 'header.php';
?>
<div class="container">
    <h1>Forums</h1>
    
    <div class="category-filter">
        <a href="forums.php">All Categories</a>
        <?php foreach ($categories as $cat): ?>
            <a href="forums.php?category=<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php foreach ($posts as $post): ?>
        <div class="forum-post">
            <h2><a href="post.php?id=<?php echo $post['id']; ?>">
                <?php echo htmlspecialchars($post['title']); ?>
            </a></h2>
            <p>By <?php echo htmlspecialchars($post['username']); ?> 
               in <?php echo htmlspecialchars($post['category_name']); ?></p>
        </div>
    <?php endforeach; ?>
</div>

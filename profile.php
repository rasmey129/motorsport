<?php
require_once 'session.php';
require_once 'database.php';
requireLogin();

$stmt = $pdo->prepare("
    SELECT * FROM users 
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT 
        p.*,
        c.name as category_name,
        COUNT(DISTINCT com.id) as comment_count
    FROM posts p
    JOIN categories c ON p.category_id = c.id
    LEFT JOIN comments com ON p.id = com.post_id
    WHERE p.user_id = ?
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$recent_posts = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET email = ?, avatar_url = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['email'],
            $_POST['avatar_url'],
            $_SESSION['user_id']
        ]);
        header('Location: profile.php?updated=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?= htmlspecialchars($user['username']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="profile-grid">
            <div class="profile-card">
                <h1>Profile</h1>
                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert success">Profile updated successfully!</div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <?php if (!empty($user['avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($user['avatar_url']) ?>" 
                             alt="Profile picture" class="profile-avatar">
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($user['username']) ?></h2>
                    <p>Member since <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
                </div>

                <form method="POST" class="profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="avatar_url">Avatar URL</label>
                        <input type="url" id="avatar_url" name="avatar_url"
                               value="<?= htmlspecialchars($user['avatar_url'] ?? '') ?>">
                    </div>
                    <button type="submit" class="update-profile-btn">Update Profile</button>
                </form>
            </div>

            <div class="recent-activity">
                <h2>Recent Posts</h2>
                <?php if (empty($recent_posts)): ?>
                    <p class="no-posts">You haven't created any posts yet.</p>
                <?php else: ?>
                    <?php foreach ($recent_posts as $post): ?>
                        <div class="activity-card">
                            <h3>
                                <a href="post.php?id=<?= $post['id'] ?>">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h3>
                            <p class="post-meta">
                                Posted in <?= htmlspecialchars($post['category_name']) ?>
                                on <?= date('F j, Y', strtotime($post['created_at'])) ?>
                                • <?= $post['comment_count'] ?> comments
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
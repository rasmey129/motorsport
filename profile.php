<?php
require_once 'session.php';
requireLogin();
require_once 'database.php';

$userId = $_SESSION['user_id'];

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch user's activity
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$recentPosts = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT comments.*, posts.title as post_title 
                       FROM comments 
                       JOIN posts ON comments.post_id = posts.id 
                       WHERE comments.user_id = ? 
                       ORDER BY comments.created_at DESC LIMIT 5");
$stmt->execute([$userId]);
$recentComments = $stmt->fetchAll();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    
    if (password_verify($currentPassword, $user['password'])) {
        if (!empty($newPassword)) {
            $password = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->execute([$email, $password, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $userId]);
        }
        $success = 'Profile updated successfully';
    } else {
        $error = 'Current password is incorrect';
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h1>Profile</h1>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="profile-section">
        <h2>Update Profile</h2>
        <form id="profileForm" method="POST" onsubmit="return validateProfileForm()">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password (leave blank to keep current):</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            <button type="submit">Update Profile</button>
        </form>
    </div>
    
    <div class="activity-section">
        <h2>Recent Activity</h2>
        
        <h3>Recent Posts</h3>
        <div class="recent-posts">
            <?php foreach ($recentPosts as $post): ?>
                <div class="activity-item">
                    <a href="/post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                    <span class="date"><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <h3>Recent Comments</h3>
        <div class="recent-comments">
            <?php foreach ($recentComments as $comment): ?>
                <div class="activity-item">
                    <p>Commented on: <a href="/post.php?id=<?php echo $comment['post_id']; ?>"><?php echo htmlspecialchars($comment['post_title']); ?></a></p>
                    <p class="comment-preview"><?php echo substr(htmlspecialchars($comment['content']), 0, 100) . '...'; ?></p>
                    <span class="date"><?php echo date('M j, Y', strtotime($comment['created_at'])); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function validateProfileForm() {
    const email = document.getElementById('email').value;
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    
    if (!email.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    if (currentPassword.length < 8) {
        alert('Current password is required');
        return false;
    }
    
    if (newPassword && newPassword.length < 8) {
        alert('New password must be at least 8 characters long');
        return false;
    }
    
    return true;
}
</script>

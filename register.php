<?php
require_once 'session.php';
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        
        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        $error = 'Registration failed. Email or username may already exist.';
    }
}

include 'header.php';
?>
<div class="container">
    <h1>Register</h1>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" onsubmit="return validateRegisterForm()">
        <div class="form-group">
            <label>Username: <input type="text" name="username" required></label>
        </div>
        <div class="form-group">
            <label>Email: <input type="email" name="email" required></label>
        </div>
        <div class="form-group">
            <label>Password: <input type="password" name="password" required></label>
        </div>
        <div class="form-group">
            <label>Confirm Password: <input type="password" name="confirm_password" required></label>
        </div>
        <button type="submit">Register</button>
    </form>
</div>
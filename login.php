<?php 
require_once 'session.php';
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        if (isset($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/');
            
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
        }
        
        header('Location: ' . BASE_URL . '/dashboard.php');        exit();
    }
}
?>

<?php include 'header.php'; ?>

<div class="container">
    <h1>Login</h1>
    <form id="loginForm" method="POST" onsubmit="return validateLoginForm()">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="remember_me"> Remember me
            </label>
        </div>
        <button type="submit">Login</button>
    </form>
</div>

<script>
function validateLoginForm() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
        alert('Please enter a valid email address');
        return false;
    }
    
    if (password.length < 6) {
        alert('Password must be at least 6 characters long');
        return false;
    }
    
    return true;
}
</script>

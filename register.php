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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>Register</h1>

            <?php if (isset($error)): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form" onsubmit="return validateRegisterForm()">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit">Register</button>
            </form>
        </div>
    </div>

    <script>
    function validateRegisterForm() {
        var password = document.getElementById('password').value;
        var confirm = document.getElementById('confirm_password').value;
        
        if (password !== confirm) {
            alert('Passwords do not match!');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
<?php
require_once 'session.php';
require_once 'database.php';
requireLogin(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'];
    
    $stmt = $pdo->prepare("
        INSERT INTO news (title, content, author_id, image_url)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([$title, $content, $_SESSION['user_id'], $image_url]);
    header('Location: news.php');
    exit();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create News Article</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="news-form-container">
            <h1>Create News Article</h1>
            
            <form method="POST" class="news-form">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required 
                           placeholder="Enter your news title">
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL (Optional)</label>
                    <input type="url" id="image_url" name="image_url" 
                           placeholder="Add an image URL for your article">
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="15" required 
                              placeholder="Write your article content here..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Publish Article</button>
                    <a href="news.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
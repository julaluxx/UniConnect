<?php
// create_thread.php
session_start();
require 'pdo.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $conn->prepare("INSERT INTO threads (title,content,category_id,author_id) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['title'], $_POST['content'], $_POST['category'], $_SESSION['user_id']]);
    header('Location: index.php');
    exit;
}
?>
<form method="post">
    Title: <input name="title" required><br>
    Content: <textarea name="content" required></textarea><br>
    Category: 
    <select name="category">
        <?php foreach($categories as $c): ?>
            <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Create Thread</button>
</form>

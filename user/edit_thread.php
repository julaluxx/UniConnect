<?php
session_start();
require 'pdo.php';
$thread_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM threads WHERE id=?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if(!$thread || $thread['author_id'] != $_SESSION['user_id']){
    echo "Not authorized"; exit;
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $stmt = $conn->prepare("UPDATE threads SET title=?, content=?, category_id=? WHERE id=?");
    $stmt->execute([$_POST['title'], $_POST['content'], $_POST['category'], $thread_id]);
    header("Location: thread.php?id=$thread_id");
    exit;
}

$categories = $conn->query("SELECT * FROM categories")->fetchAll();
?>
<form method="post">
    Title: <input name="title" value="<?=htmlspecialchars($thread['title'])?>" required><br>
    Content: <textarea name="content" required><?=htmlspecialchars($thread['content'])?></textarea><br>
    Category: 
    <select name="category">
        <?php foreach($categories as $c): ?>
            <option value="<?=$c['id']?>" <?=($c['id']==$thread['category_id'])?'selected':''?>><?=htmlspecialchars($c['name'])?></option>
        <?php endforeach; ?>
    </select><br>
    <button type="submit">Update</button>
</form>

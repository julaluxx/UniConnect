<?php
session_start();
require 'pdo.php';
$thread_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM threads WHERE id=?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if($thread && $thread['author_id']==$_SESSION['user_id']){
    $stmt = $conn->prepare("DELETE FROM threads WHERE id=?");
    $stmt->execute([$thread_id]);
}

header('Location: index.php');
exit;
?>
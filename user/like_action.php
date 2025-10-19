<?php
session_start();
require 'pdo.php';
if(!isset($_SESSION['user_id']) || !isset($_GET['thread_id'])) exit;

$thread_id = $_GET['thread_id'];
$user_id = $_SESSION['user_id'];

// ตรวจสอบว่าผู้ใช้กดไลก์แล้วหรือยัง
$stmt = $conn->prepare("SELECT * FROM likes WHERE thread_id=? AND user_id=?");
$stmt->execute([$thread_id,$user_id]);
$like = $stmt->fetch();

if($like){
    // ถ้ามีแล้ว ลบไลก์
    $stmt = $conn->prepare("DELETE FROM likes WHERE id=?");
    $stmt->execute([$like['id']]);
} else {
    // เพิ่มไลก์
    $stmt = $conn->prepare("INSERT INTO likes (thread_id,user_id) VALUES (?,?)");
    $stmt->execute([$thread_id,$user_id]);
}

header("Location: thread.php?id=$thread_id");
exit;
?>
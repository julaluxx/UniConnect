<?php
// report_action.php
session_start();
require 'pdo.php';
if(!isset($_SESSION['user_id']) || !isset($_POST['thread_id']) || !isset($_POST['description'])) exit;

$stmt = $conn->prepare("INSERT INTO reports (description, reported_by, thread_id) VALUES (?,?,?)");
$stmt->execute([$_POST['description'], $_SESSION['user_id'], $_POST['thread_id']]);

header("Location: thread.php?id=".$_POST['thread_id']);
exit;
?>
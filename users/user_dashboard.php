<?php
// users/user_dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit;
}
require '../db.php';
?>

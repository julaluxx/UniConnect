<?php
session_start();
require 'pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
    $stmt->execute([$username,$email,$password]);
    $_SESSION['user_id'] = $conn->lastInsertId();
    $_SESSION['role'] = 'user';
    header('Location: index.php');
    exit;
}
?>
<form method="post">
    Username: <input name="username" required><br>
    Email: <input name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
